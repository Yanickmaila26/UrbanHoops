@extends('layouts.app')

@section('title', $producto->PRO_Nombre)

@section('content')
    <section class="container my-15 py-5 mt-4">
        <div id="detalleProductoContainer">
            <div class="row g-5">
                <div class="col-lg-6">
                    @if ($producto->PRO_Imagen)
                        <img id="detalleImagen" src="{{ asset('storage/' . $producto->PRO_Imagen) }}"
                            alt="{{ $producto->PRO_Nombre }}"
                            class="img-fluid rounded-lg shadow-lg w-100 object-cover h-[500px]">
                    @else
                        <div
                            class="w-100 h-[500px] bg-gray-100 flex items-center justify-center text-gray-400 rounded-lg shadow-lg">
                            <span class="text-2xl font-bold">Sin Imagen</span>
                        </div>
                    @endif
                </div>
                <div class="col-lg-6">
                    <div class="alert alert-light border shadow-sm mb-4">
                        <span class="badge bg-dark text-white me-2">{{ $producto->PRO_Marca }}</span>
                        <span class="badge bg-secondary text-white">{{ $producto->PRO_Codigo }}</span>
                    </div>

                    <h1 id="detalleNombre" class="display-5 fw-bold mb-2 font-poppins">{{ $producto->PRO_Nombre }}</h1>

                    <p class="mb-3 text-2xl">
                        <span class="text-warning">★★★★☆</span> <span class="text-muted text-lg">4.0 (Reseñas)</span>
                    </p>

                    <h2 id="detallePrecio" class="text-brand display-4 mb-4 fw-bold font-poppins">
                        ${{ number_format($producto->PRO_Precio, 2) }} <span class="text-muted text-lg fw-normal">USD</span>
                    </h2>

                    <div class="mb-4">
                        <h3 class="h5 fw-bold mb-2">Descripción</h3>
                        <p id="detalleDescripcion" class="text-gray-600 leading-relaxed">
                            {{ $producto->PRO_Descripcion }}
                        </p>
                    </div>

                    <div class="mb-5">
                        <h3 class="h5 fw-bold mb-3">Talla Disponible (US) / Color</h3>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="p-3 border rounded bg-white font-monospace fw-bold shadow-sm">
                                {{ $producto->PRO_Talla }}
                            </div>
                            <div class="p-3 border rounded bg-white font-monospace fw-bold shadow-sm">
                                {{ $producto->PRO_Color }}
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button
                            class="btn btn-dark btn-lg py-3 fw-bold text-uppercase tracking-wider transition-transform hover:scale-105"
                            onclick="window.addToCart('{{ $producto->PRO_Codigo }}', '{{ $producto->PRO_Nombre }}', {{ $producto->PRO_Precio }}, '{{ asset('storage/' . $producto->PRO_Imagen) }}')">
                            Añadir al Carrito
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-cart-plus inline-block ms-2 mb-1" viewBox="0 0 16 16">
                                <path
                                    d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9z" />
                                <path
                                    d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                            </svg>
                        </button>
                    </div>

                    @if ($producto->PRO_Stock <= 5 && $producto->PRO_Stock > 0)
                        <div class="mt-3 text-red-600 font-bold animate-pulse">
                            ¡Solo quedan {{ $producto->PRO_Stock }} unidades!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
