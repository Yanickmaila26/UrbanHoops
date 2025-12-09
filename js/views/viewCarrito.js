
// ========================================
// PARTE 2: VIEW 
// ========================================

/**
 * CartView: Dibuja el carrito en la pantalla
 * 
 * Aquí es donde:
 * - Mostramos los productos en el modal
 * - Actualizamos el número rojo del carrito en la navbar
 * - Mostramos el total
 */
class CartView {
    constructor() {
        // Guardar referencias a los elementos del HTML
        // Estos pueden no existir en todas las páginas
        this.containerItems = document.getElementById('cartItems');
        this.totalElement = document.getElementById('cartTotal');
        this.countElement = document.getElementById('cartCount');
        this.btnOpenCart = document.getElementById('btnOpenCart');
        this.btnCheckout = document.getElementById('btnCheckout');
        this.modalElement = document.getElementById('modalCarrito');

        // Reintentar buscar si no se encuentran (a veces el DOM no está completamente listo)
        if (!this.btnOpenCart) {
            setTimeout(() => {
                this.btnOpenCart = document.getElementById('btnOpenCart');
                this.countElement = document.getElementById('cartCount');
                this.modalElement = document.getElementById('modalCarrito');
                this.containerItems = document.getElementById('cartItems');
                this.totalElement = document.getElementById('cartTotal');
                this.btnCheckout = document.getElementById('btnCheckout');
            }, 200);
        }
    }

    // En viewCarrito.js, actualizar el método mostrarEstadoCompra:
    mostrarEstadoCompra(estado) {
        // Ocultar todos los estados primero
        const estados = ['loading', 'success', 'error'];
        estados.forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.classList.add('d-none');
                elemento.classList.remove('hidden');
            }
        });

        // Mostrar el estado solicitado
        const elementoEstado = document.getElementById(estado);
        if (elementoEstado) {
            elementoEstado.classList.remove('d-none');

            // Si es éxito, reiniciar después de 2 segundos
            if (estado === 'success') {
                setTimeout(() => {
                    elementoEstado.classList.add('d-none');
                    this.closeModal();
                    if (window.cart) window.cart.clear();
                }, 2000);
            }

            // Si es error, ocultar después de 3 segundos
            if (estado === 'error') {
                setTimeout(() => {
                    elementoEstado.classList.add('d-none');
                }, 3000);
            }
        }
    }

    // Método para procesar pago (reemplaza el alert)
    procesarPago(total) {
        // Mostrar estado de carga
        this.mostrarEstadoCompra('loading');

        // Simular proceso de pago (en producción sería llamada a API)
        setTimeout(() => {
            // Simular éxito (90% de probabilidad) o error (10%)
            const exito = Math.random() > 0.1;

            if (exito) {
                this.mostrarEstadoCompra('success');
            } else {
                this.mostrarEstadoCompra('error');
            }
        }, 1500);
    }

    // Pintar los productos en el modal del carrito
    renderCartItems(items, onRemove, onQtyChange) {
        if (!this.containerItems) return;
        this.containerItems.innerHTML = '';

        // Si no hay nada, mostrar mensaje
        if (items.length === 0) {
            this.containerItems.innerHTML = '<div class="text-center py-3 text-muted">Tu carrito está vacío</div>';
            if (this.totalElement) this.totalElement.textContent = '$0.00';
            return;
        }

        // Crear una fila por cada producto
        for (const item of items) {
            const row = document.createElement('div');
            row.className = 'list-group-item d-flex align-items-center justify-content-between';
            row.innerHTML = `
                <div class="d-flex align-items-center">
                    <img src="${item.image || ''}" alt="${item.name}" 
                         style="width:64px;height:64px;object-fit:cover;margin-right:12px;border-radius:6px;">
                    <div>
                        <div class="fw-bold">${item.name}</div>
                        <div class="text-muted small">$${(item.price || 0).toFixed(2)} × ${item.qty}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" min="1" value="${item.qty}" 
                           data-product-id="${item.id}" 
                           class="form-control form-control-sm qty-input" 
                           style="width:80px;">
                    <button class="btn btn-sm btn-outline-danger btn-remove" 
                            data-product-id="${item.id}">Eliminar</button>
                </div>
            `;
            this.containerItems.appendChild(row);
        }

        // Cuando hace clic en eliminar
        this.containerItems.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', (ev) => {
                const id = parseInt(ev.currentTarget.getAttribute('data-product-id'), 10);
                onRemove(id);
            });
        });

        // Cuando cambia la cantidad
        this.containerItems.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', (ev) => {
                const id = parseInt(ev.currentTarget.getAttribute('data-product-id'), 10);
                const qty = parseInt(ev.currentTarget.value, 10) || 1;
                onQtyChange(id, qty);
            });
        });
    }

    updateCartCount(count) {
        // Buscar elemento en múltiples ubicaciones posibles
        if (!this.countElement) {
            this.countElement = document.getElementById('cartCount');
        }

        // Si aún no existe, buscar en otros lugares comunes
        if (!this.countElement) {
            // Buscar en el botón del carrito
            const cartButton = document.getElementById('btnOpenCart');
            if (cartButton) {
                this.countElement = cartButton.querySelector('.badge, .cart-count, #cartCount');
            }
        }

        // Si no se encuentra, crear uno
        if (!this.countElement) {
            const btnCart = document.getElementById('btnOpenCart');
            if (btnCart) {
                const span = document.createElement('span');
                span.id = 'cartCount';
                span.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                span.style.display = 'none';
                btnCart.appendChild(span);
                this.countElement = span;
            }
        }

        if (!this.countElement) {
            console.warn('No se pudo encontrar/crear el elemento cartCount');
            return;
        }

        // Actualizar contador
        this.countElement.textContent = count;

        // Mostrar u ocultar según la cantidad
        if (count > 0) {
            this.countElement.style.display = 'inline-block';
            this.countElement.classList.remove('d-none');
            this.countElement.classList.remove('hidden');
        } else {
            this.countElement.style.display = 'none';
            this.countElement.classList.add('d-none');
        }
    }

    // Actualizar el total mostrado en el modal
    updateCartTotal(total) {
        if (!this.totalElement) this.totalElement = document.getElementById('cartTotal');
        if (!this.totalElement) return;
        this.totalElement.textContent = '$' + total.toFixed(2);
    }

    // Abrir la ventana modal del carrito

    openModal() {
        if (!this.modalElement) this.modalElement = document.getElementById('modalCarrito');
        if (!this.modalElement) {
            console.warn('Modal del carrito no encontrado en la página');
            return;
        }

        try {
            // Siempre usar Bootstrap si está disponible
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                // Si ya hay una instancia de modal, usarla
                let bsModal = bootstrap.Modal.getInstance(this.modalElement);
                if (!bsModal) {
                    bsModal = new bootstrap.Modal(this.modalElement, {
                        backdrop: true,
                        keyboard: true,
                        focus: true
                    });
                }
                bsModal.show();
            } else {
                // Fallback para páginas sin Bootstrap (como index.html con Tailwind)
                this.modalElement.classList.remove('hidden');
                this.modalElement.style.display = 'block';
                document.body.classList.add('overflow-hidden');
            }
        } catch (e) {
            console.error('Error al abrir modal del carrito:', e);
            // Último intento
            if (this.modalElement) {
                this.modalElement.style.display = 'block';
                this.modalElement.classList.remove('hidden');
            }
        }
    }

    // Agregar método closeModal():
    closeModal() {
        if (!this.modalElement) this.modalElement = document.getElementById('modalCarrito');
        if (!this.modalElement) return;

        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const bsModal = bootstrap.Modal.getInstance(this.modalElement);
                if (bsModal) {
                    bsModal.hide();
                }
            } else {
                this.modalElement.classList.add('hidden');
                this.modalElement.style.display = 'none';
                document.body.classList.remove('overflow-hidden');
            }
        } catch (e) {
            console.error('Error al cerrar modal:', e);
            this.modalElement.classList.add('hidden');
            this.modalElement.style.display = 'none';
        }
    }

    /**
     * Mostrar una notificación breve en pantalla (top-right)
     * Usado para confirmar que se agregó un producto al carrito
     */
    showMessage(message, timeout = 1800) {
        // Crear contenedor si no existe
        let container = document.getElementById('cart-notification-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'cart-notification-container';
            container.style.position = 'fixed';
            container.style.top = '16px';
            container.style.right = '16px';
            container.style.zIndex = '1080';
            document.body.appendChild(container);
        }

        const el = document.createElement('div');
        el.className = 'alert alert-success shadow-sm';
        el.style.marginBottom = '8px';
        el.style.minWidth = '200px';
        el.textContent = message;
        container.appendChild(el);

        setTimeout(() => {
            el.classList.add('fade');
            el.style.transition = 'opacity 300ms';
            el.style.opacity = '0';
            setTimeout(() => container.removeChild(el), 350);
        }, timeout);
    }

    // Conectar el botón para abrir el carrito
    onOpenCartClick(callback) {
        if (!this.btnOpenCart) {
            this.btnOpenCart = document.getElementById('btnOpenCart');
        }
        if (this.btnOpenCart) {
            this.btnOpenCart.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                callback();
            });
        }
    }

    // Conectar el botón para pagar
    onCheckoutClick(callback) {
        if (!this.btnCheckout) {
            this.btnCheckout = document.getElementById('btnCheckout');
        }
        if (this.btnCheckout) {
            this.btnCheckout.addEventListener('click', callback);
        }
    }
}
