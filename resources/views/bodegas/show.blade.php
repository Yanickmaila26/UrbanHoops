@extends('components.admin-layout')

@section('page-title', 'Detalle de Bodega')

@section('content')
    <div class="max-w-4xl mx-auto p-6">
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
                    <form action="{{ route('warehouse.destroy', $bodega->BOD_Codigo) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar esta bodega?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
