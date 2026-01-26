@extends('components.admin-layout')

@section('page-title', 'Gestión de Carritos')

@section('content')
    <div class="py-6" x-data="{ deleteModal: false, selectedCarrito: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Gestión de Carritos</h2>
                <a href="{{ route('shopping-carts.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center shadow">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Carrito
                </a>
            </div>

            <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg shadow mb-6">
                <form action="{{ route('shopping-carts.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Buscar por ID o Cliente..."
                        class="flex-1 rounded-md border-gray-300 dark:bg-zinc-700 dark:text-white focus:ring-blue-500">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 rounded-md font-semibold text-white uppercase tracking-widest hover:bg-blue-700 transition">
                        Buscar
                    </button>
                    @if ($search)
                        <a href="{{ route('shopping-carts.index') }}"
                            class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 rounded-md font-semibold text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-300 transition">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Creado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach ($carritos as $carrito)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-blue-600 dark:text-blue-400">
                                    {{ $carrito->CRC_Carrito }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $carrito->cliente ? $carrito->cliente->CLI_Nombre . ' ' . $carrito->cliente->CLI_Apellido : 'Cliente Desconocido' }}<br>
                                    <span class="text-xs text-gray-500">{{ $carrito->CLI_Ced_Ruc }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $carrito->productos->count() }} productos
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400">
                                    $ {{ $carrito->getTotal() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $carrito->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('shopping-carts.edit', $carrito->CRC_Carrito) }}"
                                            class="text-yellow-600 hover:text-yellow-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button
                                            @click="deleteModal = true; selectedCarrito = { codigo: '{{ $carrito->CRC_Carrito }}', items: '{{ $carrito->productos->count() }}', total: '{{ $carrito->getTotal() }}', cliente: '{{ $carrito->cliente ? $carrito->cliente->CLI_Nombre . ' ' . $carrito->cliente->CLI_Apellido : 'Desconocido' }}' }"
                                            type="button" class="text-red-600 hover:text-red-900">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $carritos->links() }}
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="deleteModal" x-cloak @click.away="deleteModal = false" class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>

                <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex items-start mb-4">
                        <div
                            class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.96-1.333-2.73 0L3.732 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Eliminar Carrito</h3>
                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                <p class="mb-2">¿Estás seguro de que deseas eliminar este carrito?</p>
                                <div
                                    class="bg-gray-50 dark:bg-zinc-900/50 p-3 rounded border border-gray-200 dark:border-zinc-700">
                                    <p><strong class="text-gray-700 dark:text-gray-200">Cliente:</strong> <span
                                            x-text="selectedCarrito.cliente"></span></p>
                                    <p><strong class="text-gray-700 dark:text-gray-200">#Items:</strong> <span
                                            x-text="selectedCarrito.items"></span> productos</p>
                                    <p><strong class="text-gray-700 dark:text-gray-200">Total:</strong> $<span
                                            x-text="selectedCarrito.total"></span></p>
                                </div>
                                <p class="mt-3 text-red-600 dark:text-red-400 font-medium">Esta acción eliminará todos los
                                    productos del carrito.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="deleteModal = false" type="button"
                            class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-white rounded-md text-sm font-semibold hover:bg-gray-300 dark:hover:bg-zinc-600 transition">
                            Cancelar
                        </button>
                        <form :action="`/admin/shopping-carts/${selectedCarrito.codigo}`" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-semibold hover:bg-red-700 transition">
                                Eliminar Carrito
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
