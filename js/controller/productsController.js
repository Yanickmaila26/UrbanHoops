class ProductController {
    constructor() {
        this.view = new ProductView();
        this.model = new Products();
        this.init();
    }

    init() {
        this.renderProducts();
        this.setupEventListeners();
    }

    renderProducts() {
        const productos = this.model.getProducts();
        this.view.renderProductos(productos);
    }

    setupEventListeners() {
        // Evento para ver detalles en modal
        $('#listaProductos').on('click', '.btn-detalles, .card-img-top, .card-title', (e) => {
            e.stopPropagation();
            const productId = $(e.currentTarget).closest('[data-product-id]').data('product-id');
            this.mostrarDetalleModal(productId);
        });

        // Evento para agregar al carrito
        $('#listaProductos').on('click', '.btn-carrito', (e) => {
            e.stopPropagation();
            const productId = $(e.currentTarget).data('product-id');
            this.agregarAlCarrito(productId);
        });

        // Evento para cerrar modal
        $(document).on('click', '#btnCerrarModal', () => {
            this.view.cerrarModal();
        });

        // Evento para agregar desde modal
        $(document).on('click', '#btnAgregarCarrito', () => {
            const productId = $('#btnAgregarCarrito').data('product-id');
            this.agregarAlCarrito(productId);
            this.view.cerrarModal();
        });
    }

    mostrarDetalleModal(productId) {
        const producto = this.model.getProductById(productId);
        if (producto) {
            this.view.mostrarModalProducto(producto);
        }
    }

    agregarAlCarrito(productId) {
        const producto = this.model.getProductById(productId);
        if (!producto) return;

        // Usar carrito global
        if (window.cart) {
            window.cart.add(producto, 1);

            // Mostrar notificación
            if (window.cart.view && window.cart.view.showMessage) {
                window.cart.view.showMessage(` "${producto.name}" agregado al carrito`);
            }
        } else {
            alert(` "${producto.name}" agregado al carrito`);
        }
    }
}