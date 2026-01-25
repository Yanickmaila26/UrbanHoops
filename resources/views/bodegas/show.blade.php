@extends('components.admin-layout')

@section('page-title', 'Detalle de Bodega')

@section('content')
    <div class="max-w-4xl mx-auto p-6" x-data="{ deleteModal: false }">
        <div class="mb-4">
            <a href="{{ route('warehouse.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">← Volver a
                Bodegas</a>
        </div>

        <div class="bg-white dark:bg-zinc-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 flex justify-between items-center">
                <h2 class="text-xl font-bold dark:text-white">Bodega: {{ $bodega->BOD_Nombre }}</h2>
                <span
                    class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ $bodega->BOD_Codigo }}</span>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Responsable</h3>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $bodega->BOD_Responsable }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Dirección</h3>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $bodega->BOD_Direccion }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Ciudad / País</h3>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $bodega->BOD_Ciudad }},
                            {{ $bodega->BOD_Pais }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Código Postal</h3>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $bodega->BOD_CodigoPostal }}</p>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-zinc-700 flex justify-end gap-3">
                    <a href="{{ route('warehouse.edit', $bodega->BOD_Codigo) }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-white hover:bg-yellow-600 transition">
                        Editar
                    </a>
                    <button @click="deleteModal = true" type="button"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition">
                        Eliminar
                    </button>
                </div>
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
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Eliminar Bodega</h3>
                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                <p class="mb-2">¿Estás seguro de que deseas eliminar esta bodega?</p>
                                <div
                                    class="bg-gray-50 dark:bg-zinc-900/50 p-3 rounded border border-gray-200 dark:border-zinc-700">
                                    <p><strong class="text-gray-700 dark:text-gray-200">Código:</strong> <span
                                            class="font-mono">{{ $bodega->BOD_Codigo }}</span></p>
                                    <p><strong class="text-gray-700 dark:text-gray-200">Nombre:</strong>
                                        {{ $bodega->BOD_Nombre }}</p>
                                    <p><strong class="text-gray-700 dark:text-gray-200">Ciudad:</strong>
                                        {{ $bodega->BOD_Ciudad }}</p>
                                </div>
                                <p class="mt-3 text-red-600 dark:text-red-400 font-medium">Esta acción no se puede deshacer.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="deleteModal = false" type="button"
                            class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text white rounded-md text-sm font-semibold hover:bg-gray-300 dark:hover:bg-zinc-600 transition">
                            Cancelar
                        </button>
                        <form action="{{ route('warehouse.destroy', $bodega->BOD_Codigo) }}" method="POST" class="inline">
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
@endsection
