// js/views/detalleProductoView.js
class DetalleProductoView {
    constructor() {
        this.detalleContainer = document.getElementById('detalleProductoContainer');
        this.errorContainer = document.getElementById('errorProducto');
        this.imagenElement = document.getElementById('detalleImagen');
        this.nombreElement = document.getElementById('detalleNombre');
        this.precioElement = document.getElementById('detallePrecio');
        this.descripcionElement = document.getElementById('detalleDescripcion');
        this.btnAgregarCarrito = document.getElementById('btnAgregarCarritoDetalle');
    }

    renderProducto(producto) {
        if (!producto) {
            this.mostrarError();
            return null;
        }

        // Mostrar contenedor principal
        this.errorContainer.style.display = 'none';
        this.detalleContainer.style.display = 'block';

        // Llenar datos del producto
        this.imagenElement.src = producto.image || './recursos/productos/default.jpg';
        this.imagenElement.alt = producto.name;
        this.nombreElement.textContent = producto.name;
        this.precioElement.textContent = `$${producto.price.toFixed(2)} USD`;
        this.descripcionElement.textContent = producto.long_description || producto.description;

        // Configurar evento para tallas
        this.configurarTallas();

        return producto;
    }

    mostrarError() {
        this.detalleContainer.style.display = 'none';
        this.errorContainer.style.display = 'block';
    }

    configurarTallas() {
        const opcionesTalla = document.querySelectorAll('.opcion-talla');
        opcionesTalla.forEach(talla => {
            talla.addEventListener('click', (e) => {
                // Quitar clase activo de todas
                opcionesTalla.forEach(t => t.classList.remove('activo'));
                // Agregar clase activo a la seleccionada
                e.target.classList.add('activo');
            });
        });
    }

    onAgregarCarrito(callback) {
        if (this.btnAgregarCarrito) {
            // Remover listeners anteriores
            const newBtn = this.btnAgregarCarrito.cloneNode(true);
            this.btnAgregarCarrito.parentNode.replaceChild(newBtn, this.btnAgregarCarrito);
            this.btnAgregarCarrito = newBtn;

            this.btnAgregarCarrito.addEventListener('click', callback);
        }
    }

    mostrarMensaje(mensaje, tipo = 'success') {
        // Crear elemento de mensaje
        const mensajeDiv = document.createElement('div');
        mensajeDiv.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        mensajeDiv.style.zIndex = '1060';
        mensajeDiv.innerHTML = `
            <strong>${tipo === 'success' ? ' ' : ' '}${mensaje}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Agregar al body
        document.body.appendChild(mensajeDiv);

        // Auto-eliminar después de 3 segundos
        setTimeout(() => {
            if (mensajeDiv.parentNode) {
                mensajeDiv.remove();
            }
        }, 3000);
    }
}