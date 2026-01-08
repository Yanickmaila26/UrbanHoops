@extends('components.admin-layout')

@section('page-title', 'Productos')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Productos
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Gestiona el catálogo de productos e imágenes
                        </p>
                    </div>
                    <a href="{{ route('products.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Producto
                    </a>
                </div>
            </div>

            <div class="mb-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4">
                    <form action="{{ route('products.index') }}" method="GET" class="space-y-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Buscar Producto
                            </label>
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <input type="text" name="search" id="search" value="{{ $search ?? '' }}"
                                            placeholder="Buscar por código, nombre o descripción..."
                                            class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm">
                                    </div>
                                </div>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Buscar
                                </button>
                                @if ($search)
                                    <a href="{{ route('products.index') }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-300 transition">
                                        Limpiar
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($search)
                            <div class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/20 mt-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700 dark:text-blue-400">
                                            Mostrando resultados para: <span
                                                class="font-semibold">"{{ $search }}"</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Total de productos: <span class="font-semibold">{{ $productos->total() }}</span>
                    @if ($search)
                        | Resultados encontrados: <span class="font-semibold">{{ $productos->count() }}</span>
                    @endif
                </p>
            </div>

            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="bg-gray-50 dark:bg-zinc-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Imagen</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Código</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Nombre</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Marca/Color</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Stock</th>


                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-zinc-800 dark:divide-zinc-700">
                            @forelse ($productos as $producto)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            @if ($producto->PRO_Imagen)
                                                <img class="h-12 w-12 rounded-lg object-cover border border-gray-200 dark:border-zinc-600"
                                                    src="{{ asset($producto->PRO_Imagen) }}"
                                                    alt="{{ $producto->PRO_Nombre }}">
                                            @else
                                                <div
                                                    class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-zinc-700 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $producto->PRO_Codigo }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $producto->PRO_Nombre }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $producto->PRO_Descripcion }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $producto->PRO_Marca }} -
                                        {{ $producto->PRO_Color }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">
                                        ${{ number_format($producto->PRO_Precio, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="{{ $producto->PRO_Stock <= 5 ? 'text-red-500 font-bold' : '' }}">
                                            {{ $producto->PRO_Stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('products.show', $producto->PRO_Codigo) }}"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                title="Ver detalles">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('products.edit', $producto->PRO_Codigo) }}"
                                                class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400"
                                                title="Editar">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button onclick="openDeleteModal('{{ $producto->PRO_Codigo }}')"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400"
                                                title="Eliminar">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        No se encontraron productos.
                                        <a href="{{ route('products.create') }}" class="text-blue-600 underline">Crear
                                            uno
                                            nuevo</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($productos, 'links'))
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700">
                        {{ $productos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeDeleteModal()"></div>
            <div
                class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="delete-form" method="POST" class="p-6">
                    @csrf
                    @method('DELETE')
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Eliminar Producto</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">¿Estás seguro de eliminar este producto? La
                        imagen asociada también será borrada.</p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 rounded-md text-sm font-semibold">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-semibold hover:bg-red-700">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(codigo) {
            const form = document.getElementById('delete-form');
            form.action = `/admin/productos/${codigo}`;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }
    </script>
@endsection
