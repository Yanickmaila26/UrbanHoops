// js/controller/detalleProductoController.js
class DetalleProductoController {
    constructor(model, view) {
        this.model = model;
        this.view = view;
        this.producto = null;
        this.tallaSeleccionada = '9'; // Talla por defecto
        this.init();
    }

    init() {
        const productoId = this.obtenerIdProductoURL();

        if (productoId) {
            this.cargarProducto(productoId);
        } else {
            this.view.mostrarError();
        }

        // Configurar evento para agregar al carrito
        this.view.onAgregarCarrito(() => this.agregarAlCarrito());
    }

    obtenerIdProductoURL() {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            return id ? parseInt(id, 10) : null;
        } catch (error) {
            console.error('Error obteniendo ID de producto:', error);
            return null;
        }
    }

    cargarProducto(id) {
        try {
            this.producto = this.model.getProductById(id);

            if (this.producto) {
                this.view.renderProducto(this.producto);
                console.log(' Producto cargado:', this.producto);
            } else {
                console.error(' Producto no encontrado con ID:', id);
                this.view.mostrarError();
            }
        } catch (error) {
            console.error('Error cargando producto:', error);
            this.view.mostrarError();
        }
    }

    agregarAlCarrito() {
        if (!this.producto) {
            this.view.mostrarMensaje('Producto no disponible', 'error');
            return;
        }

        // Obtener talla seleccionada
        const tallaActiva = document.querySelector('.opcion-talla.activo');
        if (tallaActiva) {
            this.tallaSeleccionada = tallaActiva.textContent;
        }

        // Crear producto con talla
        const productoConTalla = {
            ...this.producto,
            talla: this.tallaSeleccionada,
            qty: 1
        };

        console.log('Agregando al carrito:', productoConTalla);

        // Usar el carrito global si existe
        if (window.cart) {
            window.cart.add(productoConTalla, 1);

            // Mostrar mensaje de confirmación
            if (window.cart.view && window.cart.view.showMessage) {
                window.cart.view.showMessage(` "${this.producto.name}" agregado al carrito`);
            } else {
                this.view.mostrarMensaje(`"${this.producto.name}" agregado al carrito`, 'success');
            }
        } else {
            console.error('Carrito no disponible');
            this.view.mostrarMensaje(`"${this.producto.name}" agregado al carrito`, 'success');
        }
    }
}