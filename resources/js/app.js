import './bootstrap';

import Alpine from 'alpinejs';

import { CartController } from './cart/cart-controller';

window.Alpine = Alpine;
Alpine.start();

// Initialize Hybrid Cart
document.addEventListener('DOMContentLoaded', () => {
    window.cartController = new CartController();

    // Global Access shim for legacy compatibility if needed
    window.cart = {
        add: (p, q) => window.cartController.add(p, q),
        remove: (id) => window.cartController.remove(id),
        clear: () => window.cartController.model.clear()
    };

    // Legacy support for inline onclicks
    window.addToCart = (id, name, price, image) => {
        const product = {
            id: id,
            name: name,
            price: parseFloat(price),
            image: image,
            qt: 9999 // Default max stock if not provided
        };
        window.cartController.add(product, 1);
    };
});