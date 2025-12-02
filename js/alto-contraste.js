document.addEventListener('DOMContentLoaded', function () {

    // Script para alto contraste
    const btnContraste = document.getElementById('btn-contraste');
    const btnAumentar = document.getElementById('btn-aumentar');
    const btnDisminuir = document.getElementById('btn-disminuir');
    const btnNormal = document.getElementById('btn-normal');
    let tamañoActual = 16;

    btnContraste.addEventListener('click', function () {
        document.body.classList.toggle('high-contrast');
    });

    btnAumentar.addEventListener('click', function () {
        tamañoActual += 2;
        document.documentElement.style.fontSize = tamañoActual + 'px';
    });

    btnDisminuir.addEventListener('click', function () {
        tamañoActual -= 2;
        if (tamañoActual < 10) tamañoActual = 10;
        document.documentElement.style.fontSize = tamañoActual + 'px';
    });

    btnNormal.addEventListener('click', function () {
        tamañoActual = 16;
        document.documentElement.style.fontSize = tamañoActual + 'px';
        document.body.classList.remove('high-contrast');
    });
});