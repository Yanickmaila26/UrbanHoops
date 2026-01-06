document.addEventListener('DOMContentLoaded', function () {
    const modalCarrito = document.getElementById('modalCarrito');

    if (modalCarrito) {
        modalCarrito.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const closeBtn = this.querySelector('[data-bs-dismiss="modal"]');
                if (closeBtn) closeBtn.click();
            }

            if (e.key === 'Tab') {
                const focusableElements = modalCarrito.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];

                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            }
        });
    }

    const productos = document.querySelectorAll('#productos-destacados article');
    productos.forEach((producto, index) => {
        producto.setAttribute('tabindex', '0');

        producto.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const link = this.querySelector('a');
                if (link) link.click();
            }
        });
    });

    const accesibilidadMenu = document.getElementById('accesibilidad-menu');
    if (accesibilidadMenu) {
        const buttons = accesibilidadMenu.querySelectorAll('button');

        buttons.forEach((button, index) => {
            button.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextIndex = (index + 1) % buttons.length;
                    buttons[nextIndex].focus();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevIndex = (index - 1 + buttons.length) % buttons.length;
                    buttons[prevIndex].focus();
                }
            });
        });
    }

    function announceToScreenReader(message, priority = 'polite') {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', priority);
        announcement.setAttribute('aria-atomic', 'true');
        announcement.classList.add('visually-hidden');
        announcement.textContent = message;

        document.body.appendChild(announcement);

        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }

    window.announceToScreenReader = announceToScreenReader;

    let isUsingKeyboard = false;

    document.addEventListener('keydown', () => {
        isUsingKeyboard = true;
    });

    document.addEventListener('mousedown', () => {
        isUsingKeyboard = false;
    });

    document.addEventListener('focusin', (e) => {
        if (isUsingKeyboard) {
            e.target.classList.add('keyboard-focus');
        }
    });

    document.addEventListener('focusout', (e) => {
        e.target.classList.remove('keyboard-focus');
    });
});