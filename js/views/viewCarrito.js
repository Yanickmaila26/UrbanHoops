
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
                        <div class="text-muted small">$${(item.price||0).toFixed(2)} × ${item.qty}</div>
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

    // Actualizar el número que aparece en el botón del carrito (navbar)
    updateCartCount(count) {
        // Rebuscar elemento en caso de que no exista (páginas diferentes)
        if (!this.countElement) this.countElement = document.getElementById('cartCount');
        if (!this.countElement) return;
        this.countElement.textContent = count;
        // Solo mostrar si hay algo en el carrito
        this.countElement.style.display = count > 0 ? 'inline-block' : 'none';
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
            // Verificar si está usando Tailwind (tiene clase hidden)
            if (this.modalElement.classList) {
                // Verificar si es un modal Tailwind (tiene la clase 'hidden')
                if (this.modalElement.classList.contains('hidden')) {
                    this.modalElement.classList.remove('hidden');
                    return;
                }
            }
            
            // Si no, intentar usar Bootstrap 5
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const bsModal = new bootstrap.Modal(this.modalElement, {
                        backdrop: 'static',
                        keyboard: true
                    });
                    bsModal.show();
                } else {
                    // Fallback si no está bootstrap
                    this.modalElement.classList.remove('hidden');
                    this.modalElement.style.display = 'block';
                }
            } catch (e) {
                console.log('Bootstrap no disponible, usando fallback');
                this.modalElement.classList.remove('hidden');
                this.modalElement.style.display = 'block';
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
