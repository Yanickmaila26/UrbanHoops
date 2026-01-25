@extends('components.admin-layout')

@section('page-title', 'Gestión de Bodegas')

@section('content')
    <div class="py-6" x-data="{ deleteModal: false, selectedBodega: { codigo: '', nombre: '', ciudad: '' } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Bodegas</h2>
                <a href="{{ route('warehouse.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow transition">
                    + Nueva Bodega
                </a>
            </div>

            <!-- Search -->
            <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg shadow mb-6">
                <form action="{{ route('warehouse.index') }}" method="GET" class="flex gap-2" x-data="{ searching: false }"
                    @submit="searching = true">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Buscar por Nombre, Código o Ciudad..."
                        class="flex-1 rounded-md border-gray-300 dark:bg-zinc-700 dark:text-white focus:ring-blue-500">
                    <button type="submit" :disabled="searching"
                        class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition dark:bg-gray-600 disabled:opacity-50 inline-flex items-center gap-2">
                        <svg x-show="!searching" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <svg x-show="searching" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-show="!searching">Buscar</span>
                        <span x-show="searching">Buscando...</span>
                    </button>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-zinc-700 border-b border-gray-200 dark:border-zinc-600">
                        <tr>
                            <th class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-300">Código</th>
                            <th class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-300">Nombre</th>
                            <th class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-300">Ubicación</th>
                            <th class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-300">Responsable</th>
                            <th class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @forelse ($bodegas as $bodega)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition">
                                <td class="px-6 py-4 dark:text-gray-300 font-mono text-sm">{{ $bodega->BOD_Codigo }}</td>
                                <td class="px-6 py-4 dark:text-gray-300 font-bold">{{ $bodega->BOD_Nombre }}</td>
                                <td class="px-6 py-4 dark:text-gray-300">
                                    {{ $bodega->BOD_Ciudad }}, {{ $bodega->BOD_Pais }}<br>
                                    <span class="text-xs text-gray-500">{{ $bodega->BOD_Direccion }}</span>
                                </td>
                                <td class="px-6 py-4 dark:text-gray-300">{{ $bodega->BOD_Responsable }}</td>
                                <td class="px-6 py-4 flex gap-2">
                                    <a href="{{ route('warehouse.show', $bodega->BOD_Codigo) }}"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Ver</a>
                                    <a href="{{ route('warehouse.edit', $bodega->BOD_Codigo) }}"
                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400">Editar</a>
                                    <button type="button"
                                        @click="deleteModal = true; selectedBodega = { codigo: '{{ $bodega->BOD_Codigo }}', nombre: '{{ $bodega->BOD_Nombre }}', ciudad: '{{ $bodega->BOD_Ciudad }}' }"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400">Eliminar</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No se encontraron bodegas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            @if ($bodegas->hasPages())
                <div class="mt-4">
                    {{ $bodegas->links() }}
                </div>
            @endif
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
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Eliminar Bodega</h3>
                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                <p class="mb-2">¿Estás seguro de que deseas eliminar esta bodega?</p>
                                <div
                                    class="bg-gray-50 dark:bg-zinc-900/50 p-3 rounded border border-gray-200 dark:border-zinc-700">
                                    <p><strong class="text-gray-700 dark:text-gray-200">Código:</strong> <span
                                            x-text="selectedBodega.codigo" class="font-mono"></span></p>
                                    <p><strong class="text-gray-700 dark:text-gray-200">Nombre:</strong> <span
                                            x-text="selectedBodega.nombre"></span></p>
                                    <p><strong class="text-gray-700 dark:text-gray-200">Ciudad:</strong> <span
                                            x-text="selectedBodega.ciudad"></span></p>
                                </div>
                                <p class="mt-3 text-red-600 dark:text-red-400 font-medium">Esta acción no se puede deshacer.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="deleteModal = false" type="button"
                            class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-white rounded-md text-sm font-semibold hover:bg-gray-300 dark:hover:bg-zinc-600 transition">
                            Cancelar
                        </button>
                        <form :action="`/admin/bodegas/${selectedBodega.codigo}`" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-semibold hover:bg-red-700 transition">
                                Eliminar Bodega
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bodegasPage', () => ({
                deleteModal: false,
                selectedBodega: {
                    codigo: '',
                    nombre: '',
                    ciudad: ''
                }
            }));
        });
    </script>
@endsection
