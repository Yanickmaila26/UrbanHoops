class ProductController {
    constructor() {
        this.view = new ProductView();
        this.model = new Products();
    }

    renderProducts() {
        const productos = this.model.getProducts();
        this.view.renderProductos(productos);
        this.agregarEventoModal();

    }

    agregarEventoModal() {
        $('#listaProductos').on('click', '.card', (event) => {
            const productId = $(event.currentTarget).data('product-id');
            const producto = this.model.getProductById(productId);
            this.view.mostrarModalProducto(producto);
        });
    }
}
