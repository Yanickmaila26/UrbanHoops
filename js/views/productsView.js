class ProductView {
    constructor() {
        this.listaProductos = $('#listaProductos');
        this.modalProducto = $('#modalProducto');
        this.modalImagen = $('#modalImagen');
        this.modalTitulo = $('#modalTitulo');
        this.modalPrecio = $('#modalPrecio');
        this.modalDescripcion = $('#modalDescripcion');
        this.modalBtnAgregarCarrito = $('#btnAgregarCarrito');
        this.modalUrl = $('#modalUrl');
    }

    renderProductos(productos) {
        let html = '';
        productos.forEach(producto => {
            html += `
                <div class="col">
                    <div class="card shadow-sm h-100" data-product-id="${producto.id}">
                        <img src="${producto.image}" class="card-img-top" alt="${producto.name}" 
                             onerror="this.onerror=null; this.src='https://placehold.co/600x400/CCCCCC/333333?text=Imagen+No+Disponible'"
                             style="cursor:pointer;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold" style="cursor:pointer;">${producto.name}</h5>
                            <p class="card-text flex-grow-1">${producto.description}</p>
                            <h4 class="text-danger mt-auto">$${producto.price.toFixed(2)}</h4>
                            <div class="d-grid gap-2">
                                <button class="btn btn-sm btn-dark btn-detalles" data-product-id="${producto.id}">
                                    Ver Detalles
                                </button>
                                <button class="btn btn-sm btn-warning btn-carrito" data-product-id="${producto.id}">
                                    🛒 Agregar al Carrito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
        });
        this.listaProductos.html(html);
    }

    mostrarModalProducto(producto) {
        this.modalImagen.attr('src', producto.image);
        this.modalTitulo.text(producto.name);
        this.modalPrecio.text(`$${producto.price.toFixed(2)} USD`);
        this.modalDescripcion.text(producto.long_description);
        this.modalBtnAgregarCarrito.data('product-id', producto.id);
        this.modalUrl.attr('href', `detalle_producto.html?id=${producto.id}`);

        // Mostrar modal con Bootstrap
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(this.modalProducto[0]);
            modal.show();
        } else {
            this.modalProducto.css('display', 'block');
        }
    }

    cerrarModal() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getInstance(this.modalProducto[0]);
            if (modal) {
                modal.hide();
            }
        } else {
            this.modalProducto.css('display', 'none');
        }
    }
}