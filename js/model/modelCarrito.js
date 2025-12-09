// ========================================
// PARTE 1: MODEL 
// ========================================

/**
 * CartModel: Se encarga de guardar los productos del carrito
 * 
 * Aquí tenemos los métodos para:
 * - Agregar productos
 * - Eliminar productos
 * - Guardar en el navegador (localStorage)
 * - Calcular el total
 */
class CartModel {
    constructor(storageKey = 'uh_cart_v1') {
        this.storageKey = storageKey;  // Clave para guardar en localStorage
        this.items = this.load();       // Cargar datos guardados
    }

    // Traer productos guardados del navegador
    load() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            console.error('No se pudo cargar el carrito:', e);
            return [];
        }
    }

    // Guardar productos en el navegador para que no se pierdan
    save() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.items));
            // Avisa a otros scripts que el carrito cambió
            document.dispatchEvent(new CustomEvent('cart:updated', {
                detail: {
                    count: this.getCount(),
                    total: this.getTotal()
                }
            }));
        } catch (e) {
            console.error('No se pudo guardar el carrito:', e);
        }
    }

    // Agregar un producto al carrito
    // Si ya existe, solo aumenta la cantidad
    add(product, qty = 1) {
        if (!product || !product.id) return;
        const existing = this.items.find(i => i.id === product.id);
        if (existing) {
            // Aumentar cantidad sin pasar del stock disponible
            existing.qty = Math.min((existing.qty || 0) + qty, product.qt || 9999);
        } else {
            // Es la primera vez que agrega este producto
            this.items.push(Object.assign({}, product, {qty: qty}));
        }
        this.save();
    }

    // Eliminar un producto del carrito
    remove(productId) {
        this.items = this.items.filter(i => i.id !== productId);
        this.save();
    }

    // Cambiar la cantidad de un producto
    // Si pone 0, lo elimina
    updateQty(productId, qty) {
        const it = this.items.find(i => i.id === productId);
        if (!it) return;
        it.qty = Math.max(0, qty);
        if (it.qty === 0) {
            this.remove(productId);
        } else {
            this.save();
        }
    }

    // Vaciar completamente el carrito
    clear() {
        this.items = [];
        this.save();
    }

    // Cuántos productos hay en total (sumando cantidades)
    getCount() {
        return this.items.reduce((sum, item) => sum + (item.qty || 0), 0);
    }

    // Cuánto cuesta todo junto
    getTotal() {
        return this.items.reduce((sum, item) => sum + (item.price || 0) * (item.qty || 0), 0);
    }

    // Devolver copia de los productos
    getItems() {
        return [...this.items];
    }
}