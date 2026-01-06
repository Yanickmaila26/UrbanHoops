<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UrbanHoops - Tienda de baloncesto urbano">
    <title>@yield('title', 'Inicio') | UrbanHoops</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-100 flex flex-col min-h-screen">

    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <div id="accesibilidad-menu"
        class="fixed top-1/2 right-0 -translate-y-1/2 bg-dark-surface p-3 rounded-l-lg shadow-xl flex flex-col gap-2 z-40 text-sm">
        <button id="btn-contraste" class="bg-brand text-black px-2 py-1 rounded font-bold hover:bg-white">Alto
            Contraste</button>
        <button id="btn-aumentar" class="bg-white text-black px-2 py-1 rounded hover:bg-gray-200">Aumentar
            letra</button>
        <button id="btn-disminuir" class="bg-white text-black px-2 py-1 rounded hover:bg-gray-200">Disminuir
            letra</button>
        <button id="btn-normal" class="bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600">Tamaño
            normal</button>
    </div>

    <header x-data="{ open: false, cartOpen: false }" class="bg-dark text-white fixed w-full top-0 z-30 shadow-md">
        <div class="container mx-auto px-4 py-2 flex items-center justify-between min-h-[80px]">

            <a href="{{ route('welcome') }}" class="flex-shrink-0 mr-4" aria-label="UrbanHoops Inicio">
                <img src="{{ asset('images/logo_fondo_osc.png') }}" alt="UrbanHoops Logo" class="h-10 md:h-12 w-auto">
            </a>

            <button @click="open = !open" class="lg:hidden text-white order-last ml-4 focus:outline-none"
                aria-label="Menú">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <nav :class="{ 'flex': open, 'hidden': !open }"
                class="hidden lg:flex flex-1 justify-between items-center absolute lg:relative top-full left-0 w-full lg:w-auto bg-dark lg:bg-transparent p-6 lg:p-0 flex-col lg:flex-row shadow-xl lg:shadow-none">

                <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 items-center lg:mx-auto">
                    <a href="{{ route('welcome') }}"
                        class="hover:text-brand transition font-medium whitespace-nowrap">Inicio</a>
                    <a href="{{ route('productos-servicios') }}"
                        class="hover:text-brand transition font-medium whitespace-nowrap">Productos</a>
                    <a href="{{ route('contacto') }}"
                        class="hover:text-brand transition font-medium whitespace-nowrap">Contacto</a>
                </div>

                <div
                    class="flex flex-col lg:flex-row gap-4 items-center mt-6 lg:mt-0 lg:ml-6 border-t lg:border-none border-gray-700 pt-4 lg:pt-0">

                    @if (Route::has('login'))
                        <div class="flex items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="hover:text-brand transition font-medium text-sm whitespace-nowrap">Mi Cuenta</a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="hover:text-brand transition font-medium text-sm uppercase tracking-wider">Entrar</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                        class="btn-brand text-black px-4 py-2 rounded text-xs font-bold uppercase whitespace-nowrap">
                                        Registro
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif

                    <div class="hidden lg:block w-px h-6 bg-gray-600 mx-2"></div>

                    <button @click="$dispatch('open-cart')"
                        class="btn-outline-brand p-2 rounded-full relative hover:bg-brand hover:text-black transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            viewBox="0 0 16 16">
                            <path
                                d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.352l.798 1.594H15.5a.5.5 0 0 1 .491.592l-1.5 8a.5.5 0 0 1-.491.408H1.832a.5.5 0 0 1-.493-.416l-1.5-8A.5.5 0 0 1 0 1.5zm4.541 6a.5.5 0 0 0-.542.493l.541.432.541-.432a.5.5 0 0 0-.541-.493zM13 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm-4-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                        </svg>
                        <span id="cartCount"
                            class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center font-bold">0</span>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <main id="main-content" class=" pt-10">
        @yield('content')
    </main>

    <footer class="bg-dark text-white py-8 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <div class="flex flex-wrap justify-center gap-4 mb-4 text-brand">
                <a href="{{ route('welcome') }}" class="hover:underline">Inicio</a> |
                <a href="{{ route('productos-servicios') }}" class="hover:underline">Productos</a> |
                <a href="{{ route('contacto') }}" class="hover:underline">Contacto</a>
            </div>
            <p class="text-gray-400">&copy; 2026 UrbanHoops. Todos los derechos reservados.</p>
        </div>
    </footer>

    <div x-data="{ open: false }" @open-cart.window="open = true" x-show="open"
        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition.opacity>

        <div class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>

        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 relative z-10" role="dialog"
                aria-modal="true">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h5 class="text-xl font-bold">🛒 Carrito de Compras</h5>
                    <button @click="open = false"
                        class="text-gray-500 hover:text-black font-bold text-2xl">&times;</button>
                </div>

                <div id="cartItems" class="max-h-96 overflow-y-auto py-4">
                    <p class="text-center text-gray-500">Tu carrito está vacío</p>
                </div>

                <div id="loading" class="hidden text-center py-2">
                    <p>Procesando...</p>
                </div>
                <div id="success" class="hidden text-center py-2 text-green-600 font-bold">
                    <p>¡Compra exitosa!</p>
                </div>

                <div class="flex justify-between items-center mt-6 pt-4 border-t">
                    <div class="text-lg font-bold">Total: <span id="cartTotal" class="text-red-600">$0.00</span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="open = false"
                            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">Cerrar</button>
                        <button id="btnCheckout"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition font-bold">Comprar
                            Ahora</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/alto-contraste.js') }}"></script>
    <script src="{{ asset('js/teclado-accesible.js') }}"></script>
</body>

</html>
