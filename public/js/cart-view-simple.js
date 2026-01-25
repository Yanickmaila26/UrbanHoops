/**
 * CartView: Dibuja el carrito en la pantalla
 */
class CartView {
    constructor() {
        this.refreshElements();
    }

    refreshElements() {
        this.containerItems = document.getElementById('cartItems');
        this.totalElement = document.getElementById('cartTotal');
        this.countElement = document.getElementById('cartCount');
        this.btnCheckout = document.getElementById('btnCheckout');
    }

    renderCartItems(items, onRemove, onQtyChange) {
        // Buscar elemento si no existe
        if (!this.containerItems) this.refreshElements();
        if (!this.containerItems) return;

        this.containerItems.innerHTML = '';

        if (items.length === 0) {
            this.containerItems.innerHTML = '<div class="text-center py-3 text-muted">Tu carrito está vacío</div>';
            if (this.totalElement) this.totalElement.textContent = '$0.00';
            return;
        }

        for (const item of items) {
            const row = document.createElement('div');
            row.className = 'list-group-item d-flex align-items-center justify-content-between';
            row.innerHTML = `
                <div class="d-flex align-items-center">
                    <img src="${item.image || ''}" alt="${item.name}" 
                         style="width:64px;height:64px;object-fit:cover;margin-right:12px;border-radius:6px;">
                    <div>
                        <div class="fw-bold">${item.name}</div>
                        ${item.talla ? `<div class="badge bg-primary mb-1">Talla: ${item.talla}</div>` : ''}
                        <div class="text-muted small">$${(item.price || 0).toFixed(2)} × ${item.qty}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" min="1" value="${item.qty}" 
                           data-product-id="${item.id}" 
                           data-product-talla="${item.talla || ''}"
                           class="form-control form-control-sm qty-input" 
                           style="width:80px;">
                    <button class="btn btn-sm btn-outline-danger btn-remove" 
                            data-product-id="${item.id}"
                            data-product-talla="${item.talla || ''}">Eliminar</button>
                </div>
            `;
            this.containerItems.appendChild(row);
        }

        this.containerItems.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', (ev) => {
                const id = ev.currentTarget.getAttribute('data-product-id');
                const talla = ev.currentTarget.getAttribute('data-product-talla') || null;
                onRemove(id, talla);
            });
        });

        this.containerItems.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', (ev) => {
                const id = ev.currentTarget.getAttribute('data-product-id');
                const talla = ev.currentTarget.getAttribute('data-product-talla') || null;
                const qty = parseInt(ev.currentTarget.value, 10) || 1;
                onQtyChange(id, qty, talla);
            });
        });
    }

    updateCartCount(count) {
        if (!this.countElement) this.refreshElements();
        if (!this.countElement) {
            const btnCart = document.getElementById('btnOpenCart');
            if (btnCart) {
                this.countElement = btnCart.querySelector('#cartCount');
            }
        }

        if (!this.countElement) return;

        this.countElement.textContent = count;
        if (count > 0) {
            this.countElement.style.display = 'inline-block';
            this.countElement.classList.remove('d-none');
        } else {
            this.countElement.style.display = 'none';
        }
    }

    updateCartTotal(total) {
        if (!this.totalElement) this.refreshElements();
        if (!this.totalElement) return;
        this.totalElement.textContent = '$' + total.toFixed(2);
    }

    openModal() {
        const modalElement = document.getElementById('modalCarrito');
        if (!modalElement) {
            console.warn('Modal del carrito no encontrado');
            return;
        }

        // Usar Bootstrap Modal API
        const bsModal = new bootstrap.Modal(modalElement);
        bsModal.show();
    }

    closeModal() {
        const modalElement = document.getElementById('modalCarrito');
        if (!modalElement) return;

        const bsModal = bootstrap.Modal.getInstance(modalElement);
        if (bsModal) {
            bsModal.hide();
        }
    }

    showMessage(message, timeout = 1800) {
        let container = document.getElementById('cart-notification-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'cart-notification-container';
            container.style.position = 'fixed';
            container.style.top = '80px';
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
            el.style.transition = 'opacity 300ms';
            el.style.opacity = '0';
            setTimeout(() => container.removeChild(el), 350);
        }, timeout);
    }
}
