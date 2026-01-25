import './bootstrap';

import Alpine from 'alpinejs';

// Desactivado: usando sistema simple en public/js/cart-*-simple.js
// import { CartController } from './cart/cart-controller';

window.Alpine = Alpine;
Alpine.start();

// CART SYSTEM DISABLED - Using public/js/cart-*-simple.js instead
// El sistema de carrito ahora está en archivos separados cargados directamente en app.blade.php