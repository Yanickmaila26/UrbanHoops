$(document).ready(function () {
    let tamañoActual = 16;

    $("#btn-contraste").click(function () {
        $("body").toggleClass("high-contrast");
    });

    $("#btn-aumentar").click(function () {
        tamañoActual += 2;
        $("body").css("font-size", tamañoActual + "px");
    });

    $("#btn-disminuir").click(function () {
        tamañoActual -= 2;
        if (tamañoActual < 10) tamañoActual = 10;
        $("body").css("font-size", tamañoActual + "px");
    });

    $("#btn-normal").click(function () {
        tamañoActual = 16;
        $("body").css("font-size", tamañoActual + "px");
        $("body").removeClass("high-contrast");
    });
});