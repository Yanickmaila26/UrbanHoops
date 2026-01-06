@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden" aria-labelledby="hero-heading">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/fondo_inicio.jpg') }}" alt="" class="hero-bg-img w-full h-full object-cover">

            <div class="absolute inset-0 bg-black/70"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4 text-center text-white">
            <h1 id="hero-heading" class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                Domina la cancha.<br>Vive el estilo.
            </h1>
            <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto text-gray-200">
                En <span class="text-brand font-bold">UrbanHoops</span> unimos el rendimiento deportivo con la autenticidad
                urbana.
            </p>
            <a href="#" class="btn btn-brand text-lg px-8 py-4">
                Explorar productos
            </a>
        </div>
    </section>

    <section id="sobre-nosotros" class="py-16 bg-white" aria-labelledby="sobre-nosotros-heading">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-7/12">
                    <h2 id="sobre-nosotros-heading" class="text-4xl font-bold text-gray-900 mb-6">
                        Acerca de UrbanHoops
                    </h2>
                    <div class="text-lg text-gray-600 space-y-4">
                        <p>UrbanHoops nació de la pasión por el baloncesto callejero y el diseño de alto rendimiento.</p>
                        <p>Ofrecemos una selección curada de zapatillas y ropa que combinan tecnología y estética.</p>
                        <p>Somos más que una tienda; somos un punto de encuentro para la cultura del baloncesto.</p>
                    </div>
                </div>
                <div class="lg:w-5/12">
                    <div class="rounded-xl overflow-hidden shadow-2xl">
                        <img src="{{ asset('images/UrbanHoops.jpg') }}" alt="Balón oficial"
                            class="w-full hover:scale-105 transition-transform duration-500">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="productos-destacados" class="py-16 bg-gray-50" aria-labelledby="productos-heading">
        <div class="container mx-auto px-4">
            <h2 id="productos-heading" class="text-4xl font-bold text-center text-gray-900 mb-12">
                Productos Destacados
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow group"
                    tabindex="0">
                    <div class="h-64 overflow-hidden">
                        <img src="{{ asset('storage/productos/lebron21.png') }}" alt="Nike LeBron 21"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Nike LeBron 21</h3>
                        <p class="text-gray-600 mb-4 h-12 overflow-hidden">Zapatillas de élite para velocidad y potencia en
                            la cancha.</p>
                        <a href="#" class="btn btn-brand w-full block">Ver Detalle</a>
                    </div>
                </article>

                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow group"
                    tabindex="0">
                    <div class="h-64 overflow-hidden">
                        <img src="{{ asset('storage/productos/balonspalding.png') }}" alt="Balón Spalding"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Balón Spalding NBA</h3>
                        <p class="text-gray-600 mb-4 h-12 overflow-hidden">Agarre premium y durabilidad excepcional.</p>
                        <a href="#" class="btn btn-brand w-full block">Comprar</a>
                    </div>
                </article>

                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow group"
                    tabindex="0">
                    <div class="h-64 overflow-hidden">
                        <img src="{{ asset('storage/productos/camisetalakers.png') }}" alt="Camiseta Lakers"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Camiseta Lakers 2025</h3>
                        <p class="text-gray-600 mb-4 h-12 overflow-hidden">Estilo icónico y comodidad transpirable.</p>
                        <a href="#" class="btn btn-brand w-full block">Comprar</a>
                    </div>
                </article>

            </div>

            <div class="text-center mt-12">
                <a href="#" class="btn btn-outline-brand text-lg px-8 py-3">Ver todos los productos</a>
            </div>
        </div>
    </section>

    <section class="py-16 bg-dark text-white text-center">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold mb-6">Únete a la Comunidad UrbanHoops</h2>
            <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                Recibe noticias, lanzamientos y eventos exclusivos de baloncesto urbano.
            </p>
            <a href="#" class="btn btn-outline-brand px-8 py-3 text-lg">Suscribirse</a>
        </div>
    </section>
@endsection
