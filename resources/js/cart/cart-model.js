/**
 * CartModel: Hybrid storage
 * Uses localStorage for guests, API for authenticated users.
 */
export class CartModel {
    constructor(storageKey = 'uh_cart_v1') {
        this.storageKey = storageKey;
        this.items = [];
        this.isAuthenticated = false;
        this.initialized = false;
    }

    async init() {
        // Check window global here to ensure it is defined
        this.isAuthenticated = window.AUTH_USER === true;

        if (this.isAuthenticated) {
            // If logged in, look for local items to sync first
            const local = this.loadLocal();
            if (local.length > 0) {
                await this.syncWithServer(local);
                this.clearLocal(); // Clear after sync
            }
            await this.fetchFromServer();
        } else {
            this.items = this.loadLocal();
        }
        this.initialized = true;
        this.notify();
    }

    loadLocal() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            console.error('Error loading cart:', e);
            return [];
        }
    }

    saveLocal() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.items));
        } catch (e) {
            console.error('Error saving cart:', e);
        }
    }

    clearLocal() {
        localStorage.removeItem(this.storageKey);
    }

    notify() {
        document.dispatchEvent(new CustomEvent('cart:updated', {
            detail: {
                count: this.getCount(),
                total: this.getTotal(),
                items: this.items
            }
        }));
    }

    // --- API Interactions ---

    async fetchFromServer() {
        try {
            const res = await fetch('/client/cart-api/');
            if (res.ok) {
                const data = await res.json();
                this.items = data.items;
            }
        } catch (e) {
            console.error('Failed to fetch cart:', e);
        }
    }

    async syncWithServer(localItems) {
        try {
            await fetch('/client/cart-api/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN
                },
                body: JSON.stringify({ items: localItems })
            });
        } catch (e) {
            console.error('Failed to sync cart:', e);
        }
    }

    async apiCall(endpoint, body) {
        try {
            const res = await fetch(`/client/cart-api/${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN
                },
                body: JSON.stringify(body)
            });
            if (res.ok) {
                const data = await res.json();
                this.items = data.items;
                this.notify();
            }
        } catch (e) {
            console.error(`API ${endpoint} failed:`, e);
        }
    }

    // --- Core Operations ---

    async add(product, qty = 1) {
        if (this.isAuthenticated) {
            await this.apiCall('add', { id: product.id, qty, talla: product.talla });
        } else {
            const existing = this.items.find(i => i.id === product.id && i.talla === product.talla);
            if (existing) {
                existing.qty = Math.min((existing.qty || 0) + qty, product.qt || 9999);
            } else {
                this.items.push(Object.assign({}, product, { qty: qty }));
            }
            this.saveLocal();
            this.notify();
        }
    }

    async remove(productId, talla = null) {
        if (this.isAuthenticated) {
            await this.apiCall('remove', { id: productId, talla });
        } else {
            this.items = this.items.filter(i => !(i.id === productId && i.talla === talla));
            this.saveLocal();
            this.notify();
        }
    }

    async updateQty(productId, qty, talla = null) {
        if (this.isAuthenticated) {
            // If qty is 0, backend handles remove
            await this.apiCall('update', { id: productId, qty, talla });
        } else {
            const it = this.items.find(i => i.id === productId && i.talla === talla);
            if (!it) return;
            it.qty = Math.max(0, qty);
            if (it.qty === 0) {
                this.remove(productId, talla);
            } else {
                this.saveLocal();
                this.notify();
            }
        }
    }

    async clear() {
        if (this.isAuthenticated) {
            await this.apiCall('clear', {});
        } else {
            this.items = [];
            this.saveLocal();
            this.notify();
        }
    }

    getCount() {
        if (!this.items) return 0;
        return this.items.reduce((sum, item) => sum + (item.qty || 0), 0);
    }

    getTotal() {
        if (!this.items) return 0;
        return this.items.reduce((sum, item) => sum + (item.price || 0) * (item.qty || 0), 0);
    }

    getItems() {
        return [...this.items];
    }
}
