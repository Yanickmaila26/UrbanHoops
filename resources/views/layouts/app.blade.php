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

</head>

<body class="font-sans antialiased bg-gray-100 flex flex-col min-h-screen">

    <!-- Menú de Accesibilidad -->
    <div id="accesibilidad-widget" style="position: fixed; bottom: 20px; left: 20px; z-index: 9999;">
        <button id="btn-toggle-accesibilidad"
            class="btn btn-primary rounded-circle shadow-lg p-0 d-flex align-items-center justify-content-center"
            style="width: 50px; height: 50px;" title="Menú de Accesibilidad">
            <span style="font-size: 24px;">♿</span>
        </button>

        <div id="accesibilidad-menu" class="d-none bg-white p-3 rounded shadow-lg border"
            style="width: 300px; position: absolute; bottom: 80px; left: 0;">
            <div class="d-grid gap-2" style="grid-template-columns: 1fr 1fr;">
                <button id="btn-contraste" class="btn btn-warning btn-sm fw-bold">
                    Alto Contraste
                </button>

                <button id="btn-normal" class="btn btn-secondary btn-sm fw-bold">
                    Normal
                </button>

                <button id="btn-aumentar" class="btn btn-light btn-sm fw-bold">
                    Aumentar +
                </button>

                <button id="btn-disminuir" class="btn btn-light btn-sm fw-bold">
                    Disminuir -
                </button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btn-toggle-accesibilidad').addEventListener('click', function() {
            var menu = document.getElementById('accesibilidad-menu');
            menu.classList.toggle('d-none');
            // Using d-block because d-grid is inside, or d-block wrapper is fine
            menu.classList.toggle('d-block');
        });
    </script>

    <!-- Header / Navbar -->
    <header class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <img src="{{ asset('images/logo_fondo_UH.png') }}" alt="UrbanHoops Logo"
                    style="height: 50px; width: auto;">
            </a>

            <!-- Botón hamburguesa -->
            <button id="navbarToggler" class="navbar-toggler" type="button">
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
                                    <li><a class="dropdown-item" href="{{ route('client.invoices') }}">Mis Facturas</a>
                                    </li>
                                    <li><a class="dropdown-item" href="{{ route('client.addresses') }}">Mis
                                            Datos de Facturación</a></li>
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
                                <a href="{{ route('client.login') }}" class="nav-link">Iniciar Sesión</a>
                            </li>
                            @if (Route::has('client.register'))
                                <li class="nav-item ms-2">
                                    <a href="{{ route('client.register') }}"
                                        class="btn btn-warning btn-sm fw-bold text-dark">Registrarse</a>
                                </li>
                            @endif
                        @endif
                    @endif

                    <!-- Cart Icon -->
                    <li class="nav-item">
                        <button id="btnOpenCart" class="btn btn-outline-warning position-relative ms-3"
                            data-bs-toggle="modal" data-bs-target="#modalCarrito">
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

    <!-- Modal del Carrito - Bootstrap Nativo -->
    <div class="modal fade" id="modalCarrito" tabindex="-1" aria-labelledby="modalCarritoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCarritoLabel">🛒 Carrito de Compras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" id="cartItems" style="max-height: 400px; overflow-y: auto;">
                    <p class="text-center text-muted">Tu carrito está vacío</p>
                </div>

                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <div class="fw-bold">
                        Total: <span id="cartTotal" class="text-danger">$0.00</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button id="btnCheckout" type="button" class="btn btn-success">Comprar Ahora</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global variables
        window.AUTH_USER = {{ Auth::guard('client')->check() ? 'true' : 'false' }};
        window.CSRF_TOKEN = "{{ csrf_token() }}";
        window.IVA_RATE = {{ config('urbanhoops.iva', 15) }};
    </script>

    <!-- Simple Cart Scripts (localStorage based) -->
    <script src="{{ asset('js/cart-model-simple.js') }}"></script>
    <script src="{{ asset('js/cart-view-simple.js') }}"></script>
    <script src="{{ asset('js/cart-controller-simple.js') }}"></script>

    <script src="{{ asset('js/alto-contraste.js') }}"></script>
    <script src="{{ asset('js/teclado-accesible.js') }}"></script>

    <!-- Navbar Toggler Script (Vanilla JS) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggler = document.getElementById('navbarToggler');
            const collapse = document.getElementById('navbarNav');

            if (toggler && collapse) {
                toggler.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Toggle the 'show' class to control visibility
                    if (collapse.classList.contains('show')) {
                        collapse.classList.remove('show');
                    } else {
                        collapse.classList.add('show');
                    }
                });
            }
        });
    </script>
</body>

</html>
