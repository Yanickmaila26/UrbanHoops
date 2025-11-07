/** CONTACTO
 * dom.js
 * PRE REQUISITOS EN EL HTML:
 * - Form con id="formContacto"
 * - Cuadro #loading
 * - Cuadro #error
 * - Cuadro #success
 */

document.addEventListener('DOMContentLoaded', function () {
    const form    = document.getElementById('formContacto');
    const loading = document.getElementById('loading');
    const success = document.getElementById('success');
    const errorEl = document.getElementById('error');

    form.addEventListener('submit', function (ev) {
        ev.preventDefault();
        form.classList.add('hidden');
        success.classList.add('hidden');
        errorEl.classList.add('hidden');
        loading.classList.remove('hidden');

        setTimeout(function () {
            form.classList.add('hidden');

            setTimeout(function () {
                loading.classList.add('hidden');
                const serverOk = Math.random() < 0.7;

                if (serverOk) {
                    success.classList.remove('hidden');
                    form.reset();
                } else {
                    errorEl.classList.remove('hidden');
                    form.classList.remove('hidden');
                }
            }, 800);
        }, 2000);
    });
});
