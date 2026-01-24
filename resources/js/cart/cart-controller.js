import { CartModel } from './cart-model';
import { CartView } from './cart-view';

export class CartController {
    constructor() {
        this.model = new CartModel();
        this.view = new CartView();

        this.model.init().then(() => {
            this.render();
        });

        this.setupEventListeners();

        // Global event listener for updates from model
        document.addEventListener('cart:updated', () => this.render());
    }

    setupEventListeners() {
        // Open Cart Button
        const btnOpen = document.getElementById('btnOpenCart');
        if (btnOpen) {
            // Clone to remove old listeners
            const newBtn = btnOpen.cloneNode(true);
            btnOpen.parentNode.replaceChild(newBtn, btnOpen);
            newBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.view.openModal();
            });
        }

        // Close Cart Button (modal overlay or X button)
        const btnClose = document.getElementById('btnCloseCart'); // Assuming an ID, or modal background
        if (btnClose) {
            btnClose.addEventListener('click', () => this.view.closeModal());
        }

        // Close on background click
        const modal = document.getElementById('modalCarrito');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) this.view.closeModal();
            });
        }

        // Checkout Button
        const btnCheckout = document.getElementById('btnCheckout');
        if (btnCheckout) {
            const newBtn = btnCheckout.cloneNode(true);
            btnCheckout.parentNode.replaceChild(newBtn, btnCheckout);
            newBtn.addEventListener('click', () => this.handleCheckout());
        }
    }

    handleCheckout() {
        if (this.model.getCount() === 0) {
            this.view.showMessage('Tu carrito está vacío');
            return;
        }

        if (window.AUTH_USER) {
            // Redirect to confirm or invoice page
            window.location.href = '/client/orders'; // Or a dedicated checkout page
        } else {
            // Guest -> Login/Register
            window.location.href = '/login';
        }
    }

    async add(product, qty = 1) {
        await this.model.add(product, qty);
        this.view.showMessage('Producto agregado correctamente');
    }

    remove(id, talla = null) {
        this.model.remove(id, talla);
    }

    updateQty(id, qty, talla = null) {
        this.model.updateQty(id, qty, talla);
    }

    render() {
        const items = this.model.getItems();
        this.view.renderCartItems(
            items,
            (id, talla) => this.remove(id, talla),
            (id, qty, talla) => this.updateQty(id, qty, talla)
        );
        this.view.updateCartCount(this.model.getCount());
        this.view.updateCartTotals(
            this.model.getSubtotal(),
            this.model.getIva(),
            this.model.getTotal()
        );
    }
}
