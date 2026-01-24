<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UrbanHoops - Tienda de baloncesto urbano">
    <title>@yield('title', 'Inicio') | UrbanHoops</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- App Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>

<body class="font-sans antialiased bg-gray-100 flex flex-col min-h-screen">

    <!-- Menú de Accesibilidad -->
    <div id="accesibilidad-menu" class="d-flex flex-column gap-2" style="z-index: 9999; width: 160px;">
        <!-- Fixed position handled in CSS now, or inline? User CSS had fixed. -->

        <button id="btn-contraste" class="btn btn-warning btn-sm fw-bold w-100">
            Alto Contraste
        </button>

        <button id="btn-aumentar" class="btn btn-light btn-sm fw-bold w-100">
            Aumentar letra
        </button>

        <button id="btn-disminuir" class="btn btn-light btn-sm fw-bold w-100">
            Disminuir letra
        </button>

        <button id="btn-normal" class="btn btn-secondary btn-sm fw-bold w-100">
            Tamaño normal
        </button>
    </div>

    <!-- Header / Navbar -->
    <header class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <img src="{{ asset('images/logo_fondo_UH.png') }}" alt="UrbanHoops Logo"
                    style="height: 50px; width: auto;">
            </a>

            <!-- Botón hamburguesa -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menú -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('welcome') ? 'active' : '' }}"
                            href="{{ route('welcome') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('productos-servicios') ? 'active' : '' }}"
                            href="{{ route('productos-servicios') }}">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contacto') ? 'active' : '' }}"
                            href="{{ route('contacto') }}">Contacto</a>
                    </li>

                    <!-- Auth Links -->
                    @if (Route::has('client.login'))
                        @if (Auth::guard('web')->check())
                            <li class="nav-item ms-3">
                                <a href="{{ url('/admin/dashboard') }}"
                                    class="btn btn-warning btn-sm fw-bold text-dark">Admin</a>
                            </li>
                        @elseif(Auth::guard('client')->check())
                            <li class="nav-item dropdown ms-3">
                                <a class="nav-link dropdown-toggle text-warning" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Hola, {{ Auth::guard('client')->user()->cliente->CLI_Nombre ?? 'Cliente' }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('client.cart') }}">Mi Carrito</a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.orders') }}">Mis Pedidos</a>
                                    </li>
                                    <li><a class="dropdown-item" href="{{ route('client.invoices') }}">Mis Facturas</a>
                                    </li>
                                    <li><a class="dropdown-item" href="{{ route('client.addresses') }}">Mis
                                            Direcciones</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('client.logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">Cerrar
                                                Sesión</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item ms-3">
                                <a href="{{ route('client.login') }}" class="nav-link">Entrar</a>
                            </li>
                            @if (Route::has('client.register'))
                                <li class="nav-item ms-2">
                                    <a href="{{ route('client.register') }}"
                                        class="btn btn-warning btn-sm fw-bold text-dark">Registro</a>
                                </li>
                            @endif
                        @endif
                    @endif

                    <!-- Cart Icon -->
                    <li class="nav-item">
                        <button id="btnOpenCart" class="btn btn-outline-warning position-relative ms-3"
                            @click="$dispatch('open-cart')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.352l.798 1.594H15.5a.5.5 0 0 1 .491.592l-1.5 8a.5.5 0 0 1-.491.408H1.832a.5.5 0 0 1-.493-.416l-1.5-8A.5.5 0 0 1 0 1.5zm4.541 6a.5.5 0 0 0-.542.493l.541.432.541-.432a.5.5 0 0 0-.541-.493zM13 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm-4-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                            </svg>
                            <span id="cartCount"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="display:none;">0</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main id="main-content" class="pt-4 mt-5">
        @yield('content')
    </main>

    <footer class="bg-dark text-white text-center py-4 mt-auto">
        <div class="container">
            <div class="mt-2">
                <a href="{{ route('welcome') }}" class="text-warning text-decoration-none me-3">Inicio</a>
                <span class="text-white">|</span>
                <a href="{{ route('productos-servicios') }}"
                    class="text-warning text-decoration-none mx-3">Productos</a>
                <span class="text-white">|</span>
                <a href="{{ route('contacto') }}" class="text-warning text-decoration-none ms-3">Contacto</a>
            </div>
            <p class="mt-3 mb-0 text-light small">
                &copy; 2026 UrbanHoops. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <!-- Modal del Carrito (Reusing existing structure but adapting classes) -->
    <div x-data="{ open: false }" @open-cart.window="open = true" @close-cart.window="open = false"
        class="modal fade" :class="{ 'show d-block': open }" tabindex="-1" aria-hidden="true"
        style="background-color: rgba(0,0,0,0.5);" x-show="open" x-transition.opacity>
        <div class="modal-dialog modal-lg modal-dialog-centered"> <!-- Centered -->
            <div class="modal-content" @click.outside="open = false">
                <div class="modal-header">
                    <h5 class="modal-title">🛒 Carrito de Compras</h5>
                    <button type="button" class="btn-close" @click="open = false"></button>
                </div>
                <div class="modal-body">
                    <div id="cartItems" class="list-group" style="max-height:400px; overflow-y:auto;">
                        <div class="text-center py-5 text-muted">Tu carrito está vacío</div>
                    </div>
                    <!-- Estados de compra -->
                    <div id="loading" class="mt-3 text-center d-none">
                        <p class="text-muted">Procesando pago...</p>
                    </div>
                    <div id="success" class="mt-3 text-center d-none">
                        <p class="text-success fw-bold">¡Compra realizada con éxito!</p>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <div class="text-lg font-weight-bold">
                        Total: <span id="cartTotal" class="text-danger">$0.00</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" @click="open = false">Cerrar</button>
                        <button id="btnCheckout" type="button" class="btn btn-success">Comprar Ahora</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.AUTH_USER = {{ Auth::guard('client')->check() ? 'true' : 'false' }};
        window.CSRF_TOKEN = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/alto-contraste.js') }}"></script>
    <script src="{{ asset('js/teclado-accesible.js') }}"></script>
</body>

</html>
