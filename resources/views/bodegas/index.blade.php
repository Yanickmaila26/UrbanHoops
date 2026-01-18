@extends('components.admin-layout')

@section('page-title', 'Gestión de Bodegas')

@section('content')
    <div class="py-6">
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
                <form action="{{ route('warehouse.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Buscar por Nombre, Código o Ciudad..."
                        class="flex-1 rounded-md border-gray-300 dark:bg-zinc-700 dark:text-white focus:ring-blue-500">
                    <button type="submit"
                        class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition dark:bg-gray-600">Buscar</button>
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
                                    <form action="{{ route('warehouse.destroy', $bodega->BOD_Codigo) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar bodega?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No se
                                    encontraron bodegas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $bodegas->links() }}
            </div>
        </div>
    </div>
@endsection
