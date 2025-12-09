// js/detalleProducto.js
document.addEventListener('DOMContentLoaded', function () {
    console.log('🔄 Inicializando página de detalle del producto...');

    // Crear modelo de productos
    const productosModel = new Products();

    // Crear vista
    const detalleView = new DetalleProductoView();

    // Crear controlador
    const detalleController = new DetalleProductoController(productosModel, detalleView);

    // Inicializar carrito
    if (typeof initializeCart === 'function') {
        initializeCart();
    } else {
        console.warn('Función initializeCart no disponible');
    }

    // Configurar botón del carrito en navbar
    setTimeout(() => {
        const btnCart = document.getElementById('btnOpenCart');
        if (btnCart) {
            // Remover onclick inline y agregar event listener
            btnCart.removeAttribute('onclick');
            btnCart.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                if (window.cart && window.cart.controller && window.cart.view) {
                    window.cart.controller.render();
                    window.cart.view.openModal();
                } else {
                    console.error('Carrito no disponible');
                    // Fallback: abrir modal directamente
                    const modal = document.getElementById('modalCarrito');
                    if (modal && bootstrap) {
                        const bsModal = new bootstrap.Modal(modal);
                        bsModal.show();
                    }
                }
            });
        }
    }, 100);

    // Función global para compatibilidad
    window.abrirCarrito = function () {
        if (window.cart && window.cart.view && window.cart.view.openModal) {
            window.cart.view.openModal();
        }
    };

    // Función global para agregar producto (compatibilidad)
    window.agregarProductoAlCarrito = function (id, nombre, precio, imagen) {
        const producto = {
            id: id,
            name: nombre,
            price: precio,
            image: imagen,
            qty: 1
        };

        if (window.cart) {
            window.cart.add(producto, 1);

            if (window.cart.view && window.cart.view.showMessage) {
                window.cart.view.showMessage(` "${nombre}" agregado al carrito`);
            }
        }
    };

    console.log(' Detalle producto inicializado correctamente');
});