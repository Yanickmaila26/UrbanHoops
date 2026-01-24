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

                    <div class="mb-5">
                        <h3 class="h5 fw-bold mb-3">Talla (US)</h3>
                        <div class="d-flex flex-wrap">
                            <template x-for="size in sizes" :key="size.talla">
                                <div class="opcion-talla me-2 mb-2 p-2 border rounded"
                                    :class="{
                                        'activo': selectedSize === size.talla,
                                        'deshabilitada': !checkStock(size)
                                    }"
                                    @click="if(checkStock(size)) selectedSize = size.talla" x-text="size.talla"
                                    role="button" :aria-disabled="!checkStock(size)" :tabindex="checkStock(size) ? 0 : -1">
                                </div>
                            </template>
                            <!-- Estado Vacío -->
                            <template x-if="!sizes || sizes.length === 0">
                                <span class="text-muted">Talla Única / No especificada</span>
                            </template>
                        </div>
                        <!-- Stock Warning -->
                        <div x-show="selectedSize" class="mt-2 text-sm text-gray-500">
                            <template x-for="size in sizes">
                                <span x-show="selectedSize === size.talla && size.stock < 5">
                                    ¡Solo quedan <span x-text="size.stock" class="text-danger fw-bold"></span> unidades!
                                </span>
                            </template>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-dark btn-lg py-3 fw-bold" id="btnAgregarCarritoDetalle"
                            :disabled="(sizes.length > 0 && !selectedSize)"
                            @click="window.addToCart('{{ $producto->PRO_Codigo }}', '{{ $producto->PRO_Nombre }}', {{ $producto->PRO_Precio }}, '{{ asset('storage/' . $producto->PRO_Imagen) }}', selectedSize)">
                            <span x-show="sizes.length === 0 || selectedSize">Añadir al Carrito</span>
                            <span x-show="sizes.length > 0 && !selectedSize">Selecciona una Talla</span>
                        </button>
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
