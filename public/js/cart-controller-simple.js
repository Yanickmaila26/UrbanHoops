/**
 * CartController: Conecta Model y View
 */
class CartController {
    constructor(model, view) {
        this.model = model;
        this.view = view;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.render();
        document.addEventListener('cart:updated', () => this.render());
    }

    setupEventListeners() {
        // Event listeners now handled at bottom of file with SweetAlert
        // This method kept for API compatibility
    }

    add(product, qty = 1) {
        this.model.add(product, qty);
        this.view.showMessage('Producto agregado al carrito');
        this.render();
    }

    remove(productId, talla = null) {
        this.model.remove(productId, talla);
        this.render();
    }

    updateQty(productId, qty, talla = null) {
        this.model.updateQty(productId, qty, talla);
        this.render();
    }

    clear() {
        this.model.clear();
        this.render();
    }

    render() {
        if (!this.view || !this.model) return;

        const items = this.model.getItems();
        const count = this.model.getCount();
        const total = this.model.getTotal();

        this.view.renderCartItems(
            items,
            (id, talla) => this.remove(id, talla),
            (id, qty, talla) => this.updateQty(id, qty, talla)
        );
        this.view.updateCartCount(count);
        this.view.updateCartTotal(total);
    }
}

/**
 * Inicialización global del carrito
 */
let globalCartController = null;
let globalCartModel = null;

function initializeCart() {
    globalCartModel = new CartModel('uh_cart_v1');
    const cartView = new CartView();

    if (!globalCartController) {
        globalCartController = new CartController(globalCartModel, cartView);
    } else {
        globalCartController.model = globalCartModel;
        globalCartController.view = cartView;
        globalCartController.setupEventListeners();
    }

    // Actualizar contador inmediatamente
    const countElement = document.getElementById('cartCount');
    if (countElement) {
        const count = globalCartModel.getCount();
        countElement.textContent = count;
        countElement.style.display = count > 0 ? 'inline-block' : 'none';
    }

    // Interfaz pública
    window.cart = {
        add: (product, qty = 1) => {
            globalCartModel.add(product, qty);
            globalCartController.render();
            cartView.showMessage('Producto agregado');
        },
        remove: (id, talla = null) => {
            globalCartModel.remove(id, talla);
            globalCartController.render();
        },
        updateQty: (id, qty, talla = null) => {
            globalCartModel.updateQty(id, qty, talla);
            globalCartController.render();
        },
        clear: () => {
            globalCartModel.clear();
            globalCartController.render();
        },
        getCount: () => globalCartModel.getCount(),
        getTotal: () => globalCartModel.getTotal(),
        getItems: () => globalCartModel.getItems()
    };

    // Función simple para agregar desde HTML
    // Acepta tallas en formato JSON array o talla individual
    window.addToCart = (id, name, price, image, tallasJson = null, tallaSeleccionada = null) => {
        let talla = tallaSeleccionada;
        let stock = 9999;

        // Si viene un JSON de tallas (desde catálogo), seleccionar la talla con más stock
        if (tallasJson && !tallaSeleccionada) {
            try {
                const tallas = typeof tallasJson === 'string' ? JSON.parse(tallasJson) : tallasJson;
                if (Array.isArray(tallas) && tallas.length > 0) {
                    // Ordenar por stock descendente y tomar la primera
                    const mejorTalla = tallas.sort((a, b) => (b.stock || 0) - (a.stock || 0))[0];
                    talla = mejorTalla.talla;
                    stock = mejorTalla.stock || 1;
                }
            } catch (e) {
                console.error('Error parseando tallas:', e);
            }
        } else if (tallaSeleccionada && tallasJson) {
            // Buscar el stock de la talla seleccionada
            try {
                const tallas = typeof tallasJson === 'string' ? JSON.parse(tallasJson) : tallasJson;
                const tallaObj = tallas.find(t => t.talla === tallaSeleccionada);
                stock = tallaObj ? tallaObj.stock : 1;
            } catch (e) {
                console.error('Error parseando tallas:', e);
            }
        }

        const product = {
            id: id,
            name: name,
            price: parseFloat(price),
            image: image,
            talla: talla,
            qt: stock
        };
        window.cart.add(product, 1);
    };


    // Checkout handler with SweetAlert
    const btnCheckout = document.getElementById('btnCheckout');
    if (btnCheckout) {
        btnCheckout.addEventListener('click', () => {
            // Check if user is authenticated
            if (!window.AUTH_USER) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Inicia Sesión',
                    text: 'Debes iniciar sesión para completar tu compra',
                    showCancelButton: true,
                    confirmButtonText: 'Ir a Login',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/login';
                    }
                });
            } else {
                // User is logged in, proceed with checkout
                const cartItems = window.cart ? window.cart.getItems() : [];
                if (cartItems.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Carrito Vacío',
                        text: 'Agrega productos antes de comprar',
                        confirmButtonColor: '#ffc107'
                    });
                    return;
                }

                // Redirect to Checkout page
                window.location.href = '/client/checkout';
            }
        });
    }

    // Sync with database if logged in
    if (window.AUTH_USER && globalCartModel) {
        globalCartModel.syncToDatabase().then(() => {
            globalCartController.render();
        });
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCart);
} else {
    initializeCart();
}

// Sincronizar con localStorage de otras pestañas
window.addEventListener('storage', (ev) => {
    if (ev.key === 'uh_cart_v1' && globalCartModel) {
        globalCartModel.items = globalCartModel.load();
        if (globalCartController) {
            globalCartController.render();
        }
    }
});
