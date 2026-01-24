@extends('layouts.app')

@section('title', $producto->PRO_Nombre)

@section('content')
    <section class="container my-15 py-5 mt-4">
        <div id="detalleProductoContainer" x-data="{
            selectedSize: null,
            sizes: {{ (function ($talla) {
                $sizes = [];
                if (strpos($talla, '-') !== false) {
                    $parts = explode('-', $talla);
                    if (is_numeric($parts[0]) && is_numeric($parts[1])) {
                        // Numeric range like 40-45
                        for ($i = $parts[0]; $i <= $parts[1]; $i++) {
                            $sizes[] = (string) $i;
                        }
                    } else {
                        // Text range like S-XXL or mixed
                        // Simple mapping for ease, or fallback to list
                        $standard_sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                        $start = array_search(trim($parts[0]), $standard_sizes);
                        $end = array_search(trim($parts[1]), $standard_sizes);
                        if ($start !== false && $end !== false && $start <= $end) {
                            $sizes = array_slice($standard_sizes, $start, $end - $start + 1);
                        } else {
                            // Fallback: just show the range string as one option or split by comma if applicable
                            $sizes = [$talla];
                        }
                    }
                } elseif (strpos($talla, ',') !== false) {
                    $sizes = array_map('trim', explode(',', $talla));
                } else {
                    $sizes = [$talla];
                }
            
                // Format for JS objects with stock simulation (since we don't have individual stock)
                $stockTotal = $producto->PRO_Stock;
                $jsSizes = [];
                foreach ($sizes as $s) {
                    $jsSizes[] = ['talla' => $s, 'stock' => floor($stockTotal / count($sizes)) ?: 1];
                }
                return json_encode($jsSizes);
            })($producto->PRO_Talla) }},
            stock: {{ $producto->PRO_Stock }},
            checkStock(size) {
                return true; // Simplified for now as we split stock virtually
            }
        }">
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

                    <div x-data="{
                        selectedSize: null,
                        sizes: {{ json_encode($producto->PRO_Talla ?? []) }}
                    }">
                        <div class="mb-5">
                            <h3 class="h5 fw-bold mb-3">Talla Disponible (US) / Color</h3>

                            <div class="mb-3">
                                <span class="d-block mb-2 font-bold text-sm text-gray-700">Selecciona Talla:</span>
                                <div class="d-flex flex-wrap gap-2">
                                    <template x-for="size in sizes" :key="size.talla">
                                        <button type="button" @click="selectedSize = size.talla"
                                            :class="selectedSize === size.talla ? 'bg-black text-white border-black' :
                                                'bg-white text-gray-900 border-gray-300 hover:border-black'"
                                            class="px-4 py-2 border rounded font-bold transition-colors min-w-[3rem]"
                                            x-text="size.talla">
                                        </button>
                                    </template>
                                    <template x-if="!sizes || sizes.length === 0">
                                        <div class="p-3 border rounded bg-white font-monospace fw-bold shadow-sm">
                                            {{ is_string($producto->PRO_Talla) ? $producto->PRO_Talla : 'Única' }}
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <span class="font-bold text-sm text-gray-700">Color:</span>
                                <span
                                    class="px-3 py-1 bg-gray-100 rounded text-dark font-bold border">{{ $producto->PRO_Color }}</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button
                                class="btn btn-dark btn-lg py-3 fw-bold text-uppercase tracking-wider transition-transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="sizes.length > 0 && !selectedSize"
                                @click="window.addToCart('{{ $producto->PRO_Codigo }}', '{{ $producto->PRO_Nombre }}', {{ $producto->PRO_Precio }}, '{{ asset('storage/' . $producto->PRO_Imagen) }}', selectedSize)">
                                <span x-show="sizes.length === 0 || selectedSize">Añadir al Carrito</span>
                                <span x-show="sizes.length > 0 && !selectedSize">Selecciona una Talla</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                    class="bi bi-cart-plus inline-block ms-2 mb-1" viewBox="0 0 16 16">
                                    <path
                                        d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9z" />
                                    <path
                                        d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                                </svg>
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
