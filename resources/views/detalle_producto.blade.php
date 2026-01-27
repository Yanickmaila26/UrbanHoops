@extends('layouts.app')

@section('title', $producto->PRO_Nombre)

@section('content')
    <section class="container my-15 py-5 mt-4">
        <div id="detalleProductoContainer">
            <div class="row">
                <div class="col-lg-5">
                    @if ($producto->PRO_Imagen)
                        <img id="detalleImagen" src="{{ asset('storage/' . $producto->PRO_Imagen) }}"
                            alt="{{ $producto->PRO_Nombre }}" class="img-fluid rounded shadow w-100">
                    @else
                        <img id="detalleImagen" src="{{ asset('images/default.jpg') }}" alt="Imagen no disponible"
                            class="img-fluid rounded shadow w-100">
                    @endif
                </div>
                <div class="col-lg-7">
                    <h1 id="detalleNombre" class="display-5 fw-bold mb-2">{{ $producto->PRO_Nombre }}</h1>
                    <p class="mb-3">
                        <span class="text-warning">★★★★☆</span> 4.0
                    </p>
                    <h2 id="detallePrecio" class="text-danger display-4 mb-4 fw-bold">
                        ${{ number_format($producto->PRO_Precio, 2) }} <span class="fs-4 text-muted">USD</span>
                    </h2>

                    <h3 class="h5 fw-bold mb-2">Descripción</h3>
                    <p id="detalleDescripcion" class="mb-4">
                        {{ $producto->PRO_Descripcion }}
                    </p>

                    <div x-data='{
                        selectedSize: null,
                        sizes: @json($producto->PRO_Talla ?? []),
                        init() {
                            // Ensure sizes is an array if it comes as a string (double encoded or missed cast)
                            if (typeof this.sizes === 'string')
                        { try { this.sizes=JSON.parse(this.sizes); } catch (e) { console.error('Error parsing sizes:', e);
                        this.sizes=[]; } } // Extra check: if it parsed but is still string (double JSON encoded) if (typeof
                        this.sizes === 'string') { try { this.sizes=JSON.parse(this.sizes); } catch (e) { this.sizes=[]; }
                        } } }'>
                        <div class="mb-5">
                            <h3 class="h5 fw-bold mb-3">Talla Disponible (US)</h3>

                            <div class="mb-3">
                                <span class="d-block mb-2 fw-bold text-sm text-muted">Selecciona Talla:</span>
                                <div class="d-flex flex-wrap gap-2" id="sizesContainer">
                                    <!-- Si PRO_Talla es un array de objetos con {talla, stock} -->
                                    <template x-if="Array.isArray(sizes) && sizes.length > 0 && sizes[0].talla">
                                        <div class="d-flex flex-wrap gap-2">
                                            <template x-for="size in sizes" :key="size.talla">
                                                <button type="button" @click="selectedSize = size.talla"
                                                    :disabled="size.stock < 1"
                                                    :class="{
                                                        'btn btn-dark': selectedSize === size
                                                            .talla,
                                                        'btn btn-outline-dark': selectedSize !== size.talla &&
                                                            size.stock >=
                                                            1,
                                                        'btn btn-outline-secondary opacity-50': size.stock < 1
                                                    }"
                                                    class="px-3 py-2 fw-bold" style="min-width: 50px;">
                                                    <span x-text="size.talla"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Fallback para formato antiguo o vacío -->
                                    <template x-if="!Array.isArray(sizes) || sizes.length === 0 || !sizes[0].talla">
                                        <div class="alert alert-info">
                                            Talla única disponible
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="fw-bold text-sm">Color:</span>
                                <span class="px-3 py-1 bg-light rounded border">{{ $producto->PRO_Color }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-dark btn-lg py-3 fw-bold text-uppercase"
                                :disabled="sizes.length > 0 && sizes[0].talla && !selectedSize"
                                @click="window.addToCart('{{ $producto->PRO_Codigo }}', '{{ addslashes($producto->PRO_Nombre) }}', {{ $producto->PRO_Precio }}, '{{ $producto->PRO_Imagen ? asset('storage/' . $producto->PRO_Imagen) : asset('images/default.jpg') }}', JSON.stringify(sizes), selectedSize)">
                                <span x-show="!sizes.length || !sizes[0].talla || selectedSize">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        fill="currentColor" class="d-inline-block me-2" viewBox="0 0 16 16">
                                        <path
                                            d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9z" />
                                        <path
                                            d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                                    </svg>
                                    Añadir al Carrito
                                </span>
                                <span x-show="sizes.length > 0 && sizes[0].talla && !selectedSize">
                                    Selecciona una Talla Primero
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!$producto->activo)
            <div id="errorProducto" class="text-center mt-5">
                <h2 class="display-6 text-danger">Producto no disponible.</h2>
                <p class="lead">Este producto ha sido removido o no está activo.</p>
                <a href="{{ route('productos-servicios') }}" class="btn btn-warning mt-3">Volver a Productos</a>
            </div>
        @endif
    </section>
@endsection
