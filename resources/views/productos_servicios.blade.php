@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <section class="bg-gray-100 py-12">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h1 class="text-4xl font-bold mb-4 font-poppins text-gray-900">Nuestra Colección</h1>
            <p class="text-lg text-gray-600">
                Explora lo último en zapatillas, ropa y accesorios de baloncesto.
            </p>
        </div>
    </section>

    <section class="container mx-auto py-12 px-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <aside class="hidden lg:block" aria-label="Filtros de productos">
                <form action="{{ route('productos-servicios') }}" method="GET"
                    class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 sticky top-24">

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-bold text-lg text-gray-900">Filtros</h2>
                        @if (request()->anyFilled(['category_id', 'subcategory_id', 'max_price', 'search']))
                            <a href="{{ route('productos-servicios') }}"
                                class="text-xs text-brand font-bold hover:underline text-danger">Limpiar</a>
                        @endif
                    </div>

                    <h3 class="font-semibold text-gray-700 mb-2 text-sm uppercase tracking-wider">Categoría</h3>
                    <ul class="space-y-2 mb-6">
                        <li>
                            <a href="{{ route('productos-servicios', request()->except(['category_id', 'subcategory_id', 'page'])) }}"
                                class="block px-3 py-2 rounded transition-colors {{ !request('category_id') && !request('subcategory_id') ? 'bg-brand text-black font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                                Todas
                            </a>
                        </li>
                        @foreach ($categorias as $cat)
                            <li>
                                <div x-data="{ open: {{ request('category_id') == $cat->CAT_Codigo || $cat->subcategorias->contains('SCT_Codigo', request('subcategory_id')) ? 'true' : 'false' }} }">
                                    <div class="flex items-center justify-between group">
                                        <a href="{{ route('productos-servicios', array_merge(request()->except(['subcategory_id', 'page']), ['category_id' => $cat->CAT_Codigo])) }}"
                                            class="flex-1 px-3 py-2 rounded transition-colors {{ request('category_id') == $cat->CAT_Codigo ? 'bg-gray-100 text-black font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-black' }}">
                                            {{ $cat->CAT_Nombre }}
                                        </a>
                                        @if ($cat->subcategorias->count() > 0)
                                            <button @click.prevent="open = !open" type="button"
                                                class="p-2 text-gray-400 hover:text-brand focus:outline-none transition-transform duration-200"
                                                :class="{ 'rotate-180': open }">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                    @if ($cat->subcategorias->count() > 0)
                                        <ul x-show="open" x-collapse
                                            class="pl-4 mt-1 space-y-1 border-l-2 border-gray-100 ml-2">
                                            @foreach ($cat->subcategorias as $sub)
                                                <li>
                                                    <a href="{{ route('productos-servicios', array_merge(request()->except(['category_id', 'page']), ['subcategory_id' => $sub->SCT_Codigo])) }}"
                                                        class="block px-3 py-1.5 text-sm rounded transition-colors {{ request('subcategory_id') == $sub->SCT_Codigo ? 'text-brand font-bold bg-brand/10' : 'text-gray-500 hover:text-brand hover:bg-gray-50' }}">
                                                        {{ $sub->SCT_Nombre }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <h3 class="font-semibold text-gray-700 mb-2 text-sm uppercase tracking-wider">Precio Máximo</h3>

                    <div class="mb-6">
                        <div class="flex justify-between text-sm mb-2 text-gray-600">
                            <span>$0</span>
                            <span class="font-bold text-black">$<span
                                    id="valorPrecio">{{ request('max_price', $maxPrice) }}</span></span>
                        </div>

                        <!-- Marcas Item -->
                        <div class="accordion-item" x-data="{ open: {{ request('brand') ? 'true' : 'false' }} }">
                            <h2 class="accordion-header" id="headingBrands">
                                <button class="accordion-button" :class="{ 'collapsed': !open }" type="button"
                                    @click="open = !open" aria-expanded="true" aria-controls="collapseBrands">
                                    Marcas
                                </button>
                            </h2>
                            <div id="collapseBrands" class="accordion-collapse" x-show="open"
                                aria-labelledby="headingBrands">
                                <div class="accordion-body">
                                    <div class="d-flex flex-column gap-2">
                                        @foreach ($allBrands as $marca)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="brand[]"
                                                    value="{{ $marca }}" id="brand-{{ $marca }}"
                                                    {{ in_array($marca, (array) request('brand', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label text-sm" for="brand-{{ $marca }}">
                                                    {{ $marca }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Precio Item -->
                        <div class="accordion-item" x-data="{ open: {{ request('apply_price') ? 'true' : 'false' }} }">
                            <h2 class="accordion-header" id="headingPrice">
                                <button class="accordion-button" :class="{ 'collapsed': !open }" type="button"
                                    @click="open = !open" aria-expanded="true" aria-controls="collapsePrice">
                                    Precio
                                </button>
                            </h2>
                            <div id="collapsePrice" class="accordion-collapse" x-show="open"
                                aria-labelledby="headingPrice">
                                <div class="accordion-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="apply_price" value="1"
                                            id="enablePrice" {{ request('apply_price') ? 'checked' : '' }}>
                                        <label class="form-check-label text-sm font-bold" for="enablePrice">
                                            Filtrar por precio
                                        </label>
                                    </div>
                                    <div class="mb-2">
                                        <div class="flex justify-between text-sm mb-1 text-gray-600">
                                            <span>$0</span>
                                            <span class="font-bold text-black">$<span
                                                    id="valorPrecio">{{ request('max_price', $maxPrice) }}</span></span>
                                        </div>
                                        <input id="rangePrecio" name="max_price" type="range" min="0"
                                            max="{{ $maxPrice }}" step="10"
                                            value="{{ request('max_price', $maxPrice) }}"
                                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-brand"
                                            oninput="updatePriceDisplay(this.value)">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-brand w-100 mt-2">Aplicar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Retain filters when submitting form -->
                    @if (request('category_id'))
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    @endif
                    @if (request('subcategory_id'))
                        <input type="hidden" name="subcategory_id" value="{{ request('subcategory_id') }}">
                    @endif
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <script>
                        function updatePriceDisplay(value) {
                            const displays = document.querySelectorAll('#valorPrecio');
                            displays.forEach(el => el.innerText = value);
                        }
                    </script>

                    <button type="submit" class="w-full btn-brand py-2 font-bold uppercase text-sm tracking-wider">
                        Aplicar Filtros
                    </button>
                </form>
            </aside>

            <section class="lg:col-span-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="listaProductos" aria-live="polite">

                    @forelse ($productos as $producto)
                        <article
                            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group h-full flex flex-col">
                            <a href="{{ route('public.products.show', $producto->PRO_Codigo) }}"
                                class="block overflow-hidden relative">
                                @if ($producto->PRO_Imagen)
                                    <img src="{{ asset('storage/' . $producto->PRO_Imagen) }}"
                                        alt="{{ $producto->PRO_Nombre }}"
                                        class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-64 bg-gray-100 flex items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif

                                <div
                                    class="absolute top-2 right-2 bg-white px-2 py-1 rounded-full text-xs font-bold shadow-sm">
                                    {{ $producto->PRO_Marca }}
                                </div>
                            </a>
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    <a href="{{ route('public.products.show', $producto->PRO_Codigo) }}"
                                        class="hover:text-brand transition-colors">{{ $producto->PRO_Nombre }}</a>
                                </h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2 flex-1" style="min-height: 40px;">
                                    {{ $producto->PRO_Descripcion }}
                                </p>

                                <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                                    <span
                                        class="text-2xl font-bold text-gray-900">${{ number_format($producto->PRO_Precio, 2) }}</span>
                                    <div class="flex gap-2">
                                        <a href="{{ route('public.products.show', $producto->PRO_Codigo) }}"
                                            class="btn btn-warning px-4 py-2 text-sm font-bold flex items-center gap-2 text-black hover:bg-yellow-400 transition-colors">
                                            Ver
                                        </a>
                                        <button type="button"
                                            onclick="window.addToCart('{{ $producto->PRO_Codigo }}', '{{ addslashes($producto->PRO_Nombre) }}', {{ $producto->PRO_Precio }}, '{{ $producto->PRO_Imagen ? asset('storage/' . $producto->PRO_Imagen) : asset('images/default.jpg') }}', '{{ json_encode($producto->PRO_Talla) }}')"
                                            class="btn btn-brand px-4 py-2 text-sm font-bold flex items-center gap-2 hover:bg-yellow-500 transition-colors text-black">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div
                            class="col-span-full py-16 text-center bg-white rounded-lg border border-dashed border-gray-300">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <p class="text-xl text-gray-500 font-medium">No se encontraron productos que coincidan con tu
                                búsqueda.</p>
                            <a href="{{ route('productos-servicios') }}"
                                class="text-brand font-bold hover:underline mt-2 inline-block">Ver todo el catálogo</a>
                        </div>
                    @endforelse

                </div>

                <!-- Pagination -->
                @if ($productos->hasPages())
                    <div class="mt-12">
                        {{ $productos->links() }}
                    </div>
                @endif
            </section>

        </div>
    </section>
@endsection
