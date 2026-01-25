/**
 * CartModel: Se encarga de guardar los productos del carrito
 * Ahora con soporte para sincronización con Base de Datos
 */
class CartModel {
    constructor(storageKey = 'uh_cart_v1') {
        this.storageKey = storageKey;
        this.items = this.load();
        this.updateTimeouts = {}; // Store timeouts for debounce
    }

    load() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    // Save only to LocalStorage (internal use)
    saveLocal() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.items));
        } catch (e) {
            // Error saving cart
        }
    }

    // Trigger update events
    notify() {
        document.dispatchEvent(new CustomEvent('cart:updated', {
            detail: {
                count: this.getCount(),
                total: this.getTotal()
            }
        }));
    }

    save() {
        this.saveLocal();
        this.notify();
    }

    add(product, qty = 1) {
        if (!product || !product.id) return;

        // Buscar por ID + Talla
        const existing = this.items.find(i => i.id === product.id && i.talla === product.talla);

        if (existing) {
            existing.qty = Math.min((existing.qty || 0) + qty, product.qt || 9999);
        } else {
            this.items.push(Object.assign({}, product, { qty: qty }));
        }

        this.save();

        // Sync with DB if logged in
        if (window.AUTH_USER) {
            this.apiAdd(product, qty);
        }
    }

    remove(productId, talla = null) {
        this.items = this.items.filter(i => !(i.id === productId && i.talla === talla));
        this.save();

        if (window.AUTH_USER) {
            this.apiRemove(productId, talla);
        }
    }

    updateQty(productId, qty, talla = null) {
        const it = this.items.find(i => i.id === productId && i.talla === talla);
        if (!it) return;

        it.qty = Math.max(0, qty);
        if (it.qty === 0) {
            this.remove(productId, talla);
        } else {
            this.save();

            // Debounce API update
            if (window.AUTH_USER) {
                const key = `${productId}_${talla || 'null'}`;

                // Clear previous timeout if exists
                if (this.updateTimeouts[key]) {
                    clearTimeout(this.updateTimeouts[key]);
                }

                // Set new timeout (500ms delay)
                this.updateTimeouts[key] = setTimeout(() => {
                    this.apiUpdate(productId, it.qty, talla);
                    delete this.updateTimeouts[key];
                }, 500);
            }
        }
    }

    clear() {
        this.items = [];
        this.save();
        // NOTE: We generally don't clear DB on local clear unless explicit action?
        // But if user clicks "empty cart", yes we should.
        // For now clear() is used mainly after checkout.
    }

    getCount() {
        return this.items.reduce((sum, item) => sum + (item.qty || 0), 0);
    }

    getTotal() {
        return this.items.reduce((sum, item) => sum + (item.price || 0) * (item.qty || 0), 0);
    }

    getItems() {
        return [...this.items];
    }

    // --- API Integration ---

    async syncToDatabase() {
        if (!window.AUTH_USER) return;

        try {
            const items = this.getItems();
            const response = await fetch('/api/cart/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN
                },
                body: JSON.stringify({ items })
            });

            if (response.ok) {
                const data = await response.json();
                this.items = data.items || [];
                this.save(); // Updates local storage with DB state (merging result)
            }
        } catch (e) {
            // fail silently
        }
    }

    async loadFromDatabase() {
        if (!window.AUTH_USER) return;

        try {
            const response = await fetch('/api/cart', {
                headers: { 'Accept': 'application/json' }
            });

            if (response.ok) {
                const data = await response.json();
                this.items = data.items || [];
                this.saveLocal();
                this.notify();
            }
        } catch (e) {
            // fail silently
        }
    }

    async apiAdd(product, qty) {
        try {
            await fetch('/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN
                },
                body: JSON.stringify({
                    id: product.id,
                    qty: qty,
                    talla: product.talla
                })
            });
        } catch (e) { }
    }

    async apiRemove(id, talla) {
        try {
            const url = `/api/cart/${id}?talla=${talla || 'null'}`;
            await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN
                }
            });
        } catch (e) { }
    }

    async apiUpdate(id, qty, talla) {
        try {
            await fetch(`/api/cart/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN
                },
                body: JSON.stringify({ qty: qty, talla: talla })
            });
        } catch (e) { }
    }
}
