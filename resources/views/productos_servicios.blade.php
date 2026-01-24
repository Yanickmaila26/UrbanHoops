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
                        @if (request()->anyFilled(['category', 'subcategory', 'brand', 'apply_price']))
                            <a href="{{ route('productos-servicios') }}"
                                class="text-xs text-brand font-bold hover:underline text-danger">Limpiar</a>
                        @endif
                    </div>

                    <div class="accordion" id="accordionFilters">
                        <!-- Categorías Item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCategories">
                                <button
                                    class="accordion-button {{ request('category') || request('subcategory') ? '' : 'collapsed' }}"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories"
                                    aria-expanded="{{ request('category') || request('subcategory') ? 'true' : 'false' }}"
                                    aria-controls="collapseCategories">
                                    Categorías
                                </button>
                            </h2>
                            <div id="collapseCategories"
                                class="accordion-collapse collapse {{ request('category') || request('subcategory') ? 'show' : '' }}"
                                aria-labelledby="headingCategories" data-bs-parent="#accordionFilters">
                                <div class="accordion-body p-0">
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($allCategories as $cat)
                                            <li>
                                                <div class="bg-gray-50 px-3 py-2 font-semibold text-sm flex justify-between items-center cursor-pointer"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#subcat-{{ $cat->CAT_Codigo }}">
                                                    {{ $cat->CAT_Nombre }}
                                                    <small>▼</small>
                                                </div>
                                                <div class="collapse show" id="subcat-{{ $cat->CAT_Codigo }}">
                                                    <ul class="list-group list-group-flush">
                                                        <!-- Link for Parent Category -->
                                                        <li class="list-group-item py-1 border-0">
                                                            <a href="{{ route('productos-servicios', array_merge(request()->except(['category', 'subcategory', 'page']), ['category' => $cat->CAT_Codigo])) }}"
                                                                class="text-sm text-gray-600 hover:text-brand {{ request('category') == $cat->CAT_Codigo ? 'fw-bold text-brand' : '' }}">
                                                                Ver Todo {{ $cat->CAT_Nombre }}
                                                            </a>
                                                        </li>
                                                        @foreach ($cat->subcategorias as $sub)
                                                            <li class="list-group-item py-1 border-0 ps-4">
                                                                <a href="{{ route('productos-servicios', array_merge(request()->except(['category', 'subcategory', 'page']), ['subcategory' => $sub->SCT_Codigo])) }}"
                                                                    class="text-sm text-gray-600 hover:text-brand {{ request('subcategory') == $sub->SCT_Codigo ? 'fw-bold text-brand' : '' }}">
                                                                    {{ $sub->SCT_Nombre }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Marcas Item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingBrands">
                                <button class="accordion-button {{ request('brand') ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseBrands"
                                    aria-expanded="{{ request('brand') ? 'true' : 'false' }}"
                                    aria-controls="collapseBrands">
                                    Marcas
                                </button>
                            </h2>
                            <div id="collapseBrands"
                                class="accordion-collapse collapse {{ request('brand') ? 'show' : '' }}"
                                aria-labelledby="headingBrands" data-bs-parent="#accordionFilters">
                                <div class="accordion-body">
                                    <div class="d-flex flex-column gap-2">
                                        @foreach ($allBrands as $marca)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="brand[]"
                                                    value="{{ $marca }}" id="brand-{{ $marca }}"
                                                    {{ in_array($marca, (array) request('brand', [])) ? 'checked' : '' }}
                                                    onchange="this.form.submit()">
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
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPrice">
                                <button class="accordion-button {{ request('apply_price') ? '' : 'collapsed' }}"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrice"
                                    aria-expanded="{{ request('apply_price') ? 'true' : 'false' }}"
                                    aria-controls="collapsePrice">
                                    Precio
                                </button>
                            </h2>
                            <div id="collapsePrice"
                                class="accordion-collapse collapse {{ request('apply_price') ? 'show' : '' }}"
                                aria-labelledby="headingPrice" data-bs-parent="#accordionFilters">
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
                                            oninput="document.getElementById('valorPrecio').innerText = this.value">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-brand w-100 mt-2">Aplicar</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2 flex-1">{{ $producto->PRO_Descripcion }}
                                </p>

                                <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                                    <span
                                        class="text-2xl font-bold text-gray-900">${{ number_format($producto->PRO_Precio, 2) }}</span>
                                    <div class="flex gap-2">
                                        <a href="{{ route('public.products.show', $producto->PRO_Codigo) }}"
                                            class="btn btn-warning px-4 py-2 text-sm font-bold flex items-center gap-2 text-black hover:bg-yellow-400 transition-colors">
                                            Ver
                                        </a>
                                        <a href="{{ route('public.products.show', $producto->PRO_Codigo) }}"
                                            class="btn-brand px-4 py-2 text-sm font-bold flex items-center gap-2 group-hover:bg-brand-dark transition-colors text-black">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                        </a>
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
