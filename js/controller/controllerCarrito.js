
// ========================================
// PARTE 3: CONTROLLER 
// ========================================

/**
 * CartController: Conecta todo
 * 
 * Cuando el usuario hace algo (como hacer clic):
 * 1. El controller lo detecta
 * 2. Le dice al modelo que cambie los datos
 * 3. Le dice a la vista que se redibuje
 */
class CartController {
    constructor(model, view) {
        this.model = model;
        this.view = view;
        this.init();
    }

    // Configurar todo cuando carga la página
    init() {
        // Configurar event listeners inmediatamente
        this.setupEventListeners();
        
        // Renderizar la vista
        this.render();
        
        // Si el modelo cambia, actualizar la vista
        document.addEventListener('cart:updated', () => this.render());
    }

    // Escuchar cuando el usuario hace clic en botones
    setupEventListeners() {
        // Buscar el botón si no se encontró antes
        if (!this.btnOpenCart) {
            this.btnOpenCart = document.getElementById('btnOpenCart');
        }
        if (!this.btnCheckout) {
            this.btnCheckout = document.getElementById('btnCheckout');
        }

        // Botón para abrir el carrito
        if (this.btnOpenCart) {
            // Remover listener anterior si existe para evitar duplicados
            const newBtn = this.btnOpenCart.cloneNode(true);
            if (this.btnOpenCart.parentNode) {
                this.btnOpenCart.parentNode.replaceChild(newBtn, this.btnOpenCart);
                this.btnOpenCart = newBtn;
            }
            
            const openModalHandler = (e) => {
                console.log('🛒 Botón del carrito clickeado');
                e.preventDefault();
                e.stopPropagation();
                this.render();
                // Abrir el modal a través de la vista
                if (this.view && typeof this.view.openModal === 'function') {
                    console.log('🛒 Abriendo modal...');
                    this.view.openModal();
                }
            };
            
            this.btnOpenCart.addEventListener('click', openModalHandler, false);
        }

        // Botón de pagar
        if (this.btnCheckout) {
            this.btnCheckout.addEventListener('click', () => {
                const total = this.model.getTotal();
                alert(`Proceder a pago. Total: $${total.toFixed(2)}`);
                // Aquí se conectaría con Stripe, PayPal, etc.
            });
        }
    }

    // Agregar producto
    add(product, qty = 1) {
        this.model.add(product, qty);
        this.render();
    }

    // Eliminar producto
    remove(productId) {
        this.model.remove(productId);
        this.render();
    }

    // Cambiar cantidad
    updateQty(productId, qty) {
        this.model.updateQty(productId, qty);
        this.render();
    }

    // Vaciar todo
    clear() {
        this.model.clear();
        this.render();
    }

    // Actualizar lo que se ve en pantalla
    render() {
        if (!this.view || !this.model) return;
        
        const items = this.model.getItems();
        const count = this.model.getCount();
        const total = this.model.getTotal();

        // Redibujar todo
        this.view.renderCartItems(
            items,
            (id) => this.remove(id),
            (id, qty) => this.updateQty(id, qty)
        );
        this.view.updateCartCount(count);
        this.view.updateCartTotal(total);
    }
}

// ========================================
// INICIALIZAR TODO
// ========================================

/**
 * Instancia GLOBAL del carrito que persiste entre páginas
 * Se crea una sola vez y se mantiene viva
 */
let globalCartController = null;
let globalCartModel = null;

function initializeCart() {
    console.log('🛒 Inicializando carrito...');
    
    // Crear/Recrear el modelo (datos) - carga del localStorage siempre
    globalCartModel = new CartModel('uh_cart_v1');
    
    // Crear la vista (lo que se ve) con los elementos de la página actual
    const cartView = new CartView();
    
    // Si ya existe el controller, no crear de nuevo
    if (!globalCartController) {
        globalCartController = new CartController(globalCartModel, cartView);
        console.log('🛒 CartController creado');
    } else {
        // Pero actualizar referencias de la vista
        globalCartController.model = globalCartModel;
        globalCartController.view = cartView;
        // Re-setup event listeners con la nueva vista
        globalCartController.setupEventListeners();
        console.log('🛒 CartController actualizado');
    }
    
    // Re-setup de event listeners para asegurar que el botón tenga listeners
    globalCartController.setupEventListeners();
    
    // Actualizar el contador visual inmediatamente
    const countElement = document.getElementById('cartCount');
    if (countElement) {
        const count = globalCartModel.getCount();
        countElement.textContent = count;
        countElement.style.display = count > 0 ? 'inline-block' : 'none';
    }
    
    // Interfaz pública para agregar/eliminar productos desde otros scripts
    window.cart = {
        add: (product, qty = 1) => {
            globalCartModel.add(product, qty);
            globalCartController.render();
        },
        remove: (id) => {
            globalCartModel.remove(id);
            globalCartController.render();
        },
        updateQty: (id, qty) => {
            globalCartModel.updateQty(id, qty);
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

    // Exponer referencias útiles para otros scripts (por ejemplo para mostrar mensajes)
    window.cart.view = cartView;
    window.cart.controller = globalCartController;
    
    console.log('🛒 Carrito inicializado correctamente');
}

// Inicializar cuando el DOM está listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('📄 DOM Content Loaded');
        initializeCart();
    });
} else {
    // DOM ya está cargado
    console.log('📄 DOM ya estaba cargado');
    initializeCart();
}

// También ejecutar al cargar la ventana
window.addEventListener('load', () => {
    console.log('🪟 Window loaded event');
    if (globalCartController) {
        globalCartController.setupEventListeners();
    }
});

// Sincronizar carrito cuando la página se vuelve visible (al volver de otra tab)
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        // Recargar el contador del localStorage
        if (globalCartModel) {
            globalCartModel.items = globalCartModel.load();
            const countElement = document.getElementById('cartCount');
            if (countElement) {
                const count = globalCartModel.getCount();
                countElement.textContent = count;
                countElement.style.display = count > 0 ? 'inline-block' : 'none';
            }
        }
    }
});

// Escuchar cambios de localStorage desde otras pestañas (para sincronizar inmediatamente)
window.addEventListener('storage', (ev) => {
    if (!ev.key) return;
    if (ev.key === 'uh_cart_v1') {
        try {
            if (globalCartModel) {
                globalCartModel.items = globalCartModel.load();
                if (globalCartController && globalCartController.view) {
                    globalCartController.render();
                }
            }
        } catch (e) {
            console.error('Error al sincronizar carrito desde storage event', e);
        }
    }
});
