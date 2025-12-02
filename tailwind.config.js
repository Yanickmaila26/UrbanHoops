/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./*.html", "./js/**/*.js"],
    theme: {
        extend: {
            colors: {
                'urban-orange': '#ff6600', // color-energia
                'urban-dark': '#1c1c1c',   // color-fondo-oscuro
                'urban-light': '#f2f2f2',  // color-neutro
                'urban-blue': '#007bff',   // color-enfasis
                'urban-text': '#1c1c1c',   // color-texto
                'urban-light-text': '#ffffff', // color-texto-claro
            },
            fontFamily: {
                'sans': ['Poppins', 'Roboto', 'sans-serif'],
            },
        },
    },
    plugins: [],
}