/** CONTACTO
 * dom.js
 * PRE REQUISITOS EN EL HTML:
 * - Form con id="formContacto"
 * - Cuadro #loading
 * - Cuadro #error
 * - Cuadro #success
 */

(function () {
    'use strict';

    // Helpers
    const $ = (sel) => document.querySelector(sel);
    const show = (el) => el && el.classList.remove('hidden');
    const hide = (el) => el && el.classList.add('hidden');

    // Simulación de respuesta servidor
    function simulatedServerResponse(formData) {
        return new Promise((resolve) => {
            setTimeout(() => {
                const ok = Math.random() < 0.7; // 70% éxito
                resolve(!!ok);
            }, 500);
        });
    }

    function init() {
        const form = $('#formContacto');
        const loading = $('#loading');
        const success = $('#success');
        const error = $('#error');

        if (!form || !loading || !success || !error) {
            console.error('dom.js: faltan elementos en el DOM');
            return;
        }

        form.addEventListener('submit', (ev) => {
            ev.preventDefault();

            hide(success);
            hide(error);

            show(loading);

            setTimeout(() => {
                hide(form);

                const formData = new FormData(form);

                simulatedServerResponse(formData).then((serverOk) => {
                    hide(loading);

                    if (serverOk) {
                        show(success);
                    } else {
                        show(error);
                        show(form);
                    }
                });
            }, 2000); // 2 segundos
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
