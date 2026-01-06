document.addEventListener('DOMContentLoaded', function () {

    const btnContraste = document.getElementById('btn-contraste');
    const btnAumentar = document.getElementById('btn-aumentar');
    const btnDisminuir = document.getElementById('btn-disminuir');
    const btnNormal = document.getElementById('btn-normal');

    // Variables de estado
    let tamañoActual = 16;
    let isHighContrast = false;

    // Event listeners
    btnContraste.addEventListener('click', function () {
        if (document.body.classList.contains('high-contrast')) {
            quitarAltoContraste();
        } else {
            aplicarAltoContraste();
        }
        isHighContrast = document.body.classList.contains('high-contrast');
        this.setAttribute('aria-pressed', isHighContrast);

        localStorage.setItem('urbanhoops-high-contrast', isHighContrast);

    });

    btnAumentar.addEventListener('click', function () {
        tamañoActual += 2;
        if (tamañoActual > 24) {
            tamañoActual = 24;
        }
        document.documentElement.style.fontSize = tamañoActual + 'px';
        localStorage.setItem('urbanhoops-font-size', tamañoActual.toString());
    });

    btnDisminuir.addEventListener('click', function () {
        tamañoActual -= 2;
        if (tamañoActual < 12) {
            tamañoActual = 12;
        }
        document.documentElement.style.fontSize = tamañoActual + 'px';
        localStorage.setItem('urbanhoops-font-size', tamañoActual.toString());
    });

    btnNormal.addEventListener('click', function () {
        // Restaurar tamaño
        tamañoActual = 16;
        document.documentElement.style.fontSize = tamañoActual + 'px';

        // Desactivar alto contraste
        quitarAltoContraste();
        isHighContrast = false;
        btnContraste.setAttribute('aria-pressed', 'false');

        // Limpiar localStorage
        localStorage.removeItem('urbanhoops-high-contrast');
        localStorage.removeItem('urbanhoops-font-size');
    });

    // Funciones auxiliares
    function aplicarAltoContraste() {
        document.body.classList.add('high-contrast');

        // Asegurar que todos los elementos tengan los estilos correctos
        setTimeout(function () {
            const elementos = document.querySelectorAll('header, section, footer, .card, .modal-content');
            elementos.forEach(el => {
                el.classList.add('high-contrast-applied');
            });
        }, 100);
    }

    function quitarAltoContraste() {
        document.body.classList.remove('high-contrast');
    }

});