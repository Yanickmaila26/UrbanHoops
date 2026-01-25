/**
 * CartView: Handles DOM updates
 */
export class CartView {
    constructor() {
        this.refreshElements();
    }

    refreshElements() {
        this.containerItems = document.getElementById('cartItems');
        // If strict elements are needed
        this.totalElement = document.getElementById('cartTotal');
        this.countElement = document.getElementById('cartCount') || document.querySelector('.badge');
    }

    renderCartItems(items, onRemove, onQtyChange) {
        if (!this.containerItems) this.refreshElements();
        if (!this.containerItems) return;

        this.containerItems.innerHTML = '';

        if (items.length === 0) {
            this.containerItems.innerHTML = '<div class="text-center py-3 text-muted">Tu carrito está vacío</div>';
            if (this.totalElement) this.totalElement.textContent = '$0.00';
            return;
        }

        items.forEach(item => {
            const row = document.createElement('div');
            // Simplified class structure for generic usage, adapt to current CSS framework if needed (Bootstrap/Tailwind)
            row.className = 'flex items-center justify-between p-2 border-b';
            // Fallback for bootstrap if Tailwind not fully loaded:
            if (!row.classList.contains('flex')) row.className = 'list-group-item d-flex align-items-center justify-content-between';

            row.innerHTML = `
                <div class="flex items-center">
                    <img src="${item.image}" alt="${item.name}" class="h-16 w-16 object-cover rounded mr-3">
                    <div>
                        <div class="font-bold text-sm">${item.name}</div>
                        ${item.talla ? `<div class="text-xs text-brand font-bold bg-gray-100 dark:bg-zinc-800 rounded px-1 inline-block mb-1">Talla: ${item.talla}</div>` : ''}
                        <div class="text-gray-500 text-xs">$${parseFloat(item.price).toFixed(2)} x ${item.qty}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="number" min="1" value="${item.qty}" 
                           data-product-id="${item.id}" 
                           data-product-talla="${item.talla || ''}"
                           class="qty-input w-16 border rounded p-1 text-center" 
                           style="width: 60px;">
                    <button class="btn-remove text-red-500 hover:text-red-700" 
                            data-product-id="${item.id}"
                            data-product-talla="${item.talla || ''}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            `;
            this.containerItems.appendChild(row);
        });

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

        // Try fallback search if still null
        if (!this.countElement) {
            const btn = document.getElementById('btnOpenCart');
            if (btn) this.countElement = btn.querySelector('#cartCount') || btn.querySelector('.badge');
        }

        if (this.countElement) {
            this.countElement.textContent = count;
            this.countElement.style.display = count > 0 ? 'flex' : 'none'; // 'flex' for tailwind badges usually
            if (count > 0) {
                this.countElement.classList.remove('hidden', 'd-none');
            } else {
                this.countElement.classList.add('hidden');
            }
        }
    }

    updateCartTotals(subtotal, iva, total) {
        if (!this.totalElement) this.refreshElements();

        // Ensure elements exist (added in layout)
        const subElement = document.getElementById('cartSubtotal');
        const ivaElement = document.getElementById('cartIva');

        if (subElement) subElement.textContent = '$' + parseFloat(subtotal).toFixed(2);
        if (ivaElement) ivaElement.textContent = '$' + parseFloat(iva).toFixed(2);
        if (this.totalElement) this.totalElement.textContent = '$' + parseFloat(total).toFixed(2);
    }

    openModal() {
        // Dispatch event for AlpineJS to catch
        window.dispatchEvent(new CustomEvent('open-cart'));
    }

    closeModal() {
        // Dispatch event for AlpineJS to catch (needs listener in Blade)
        window.dispatchEvent(new CustomEvent('close-cart'));
    }

    showMessage(message, type = 'success') {
        const div = document.createElement('div');
        // Tailwind styling to mimic Bootstrap alert-success
        // bg-green-100 text-green-800 border-green-200 -> roughly equivalent
        // fixed top-24 right-4 z-50 -> positioning
        div.className = 'fixed top-24 right-4 z-50 flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-100 shadow-lg transition-opacity duration-300';
        div.role = 'alert';

        div.innerHTML = `
            <svg class="flex-shrink-0 inline w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 16 16" role="img" aria-label="Success:">
                <use xlink:href="#check-circle-fill"/>
            </svg>
            <div class="font-medium">
                ${message}
            </div>
        `;

        document.body.appendChild(div);

        // Fade out
        setTimeout(() => {
            div.style.opacity = '0';
            setTimeout(() => div.remove(), 300);
        }, 3000);
    }
}
