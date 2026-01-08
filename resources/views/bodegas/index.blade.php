@extends('components.admin-layout')

@section('page-title', 'Kardex - Movimientos de Bodega')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Movimientos de Bodega</h2>
                <a href="{{ route('warehouse.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center shadow">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Movimiento
                </a>
            </div>

            <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg shadow mb-6">
                <form action="{{ route('warehouse.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Buscar por código, OC o producto..."
                        class="flex-1 rounded-md border-gray-300 dark:bg-zinc-700 dark:text-white focus:ring-blue-500">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-zinc-800 dark:bg-zinc-600 text-white rounded-md hover:bg-zinc-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Buscar
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-200 uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-4">Código</th>
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4">Transacción</th>
                                <th class="px-6 py-4">Referencia</th>
                                <th class="px-6 py-4 text-center">Cantidad</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                            @forelse($movimientos as $mov)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-blue-600 dark:text-blue-400">
                                        {{ $mov->BOD_Codigo }}
                                    </td>
                                    <td class="px-6 py-4 dark:text-gray-300">
                                        {{ $mov->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $mov->transaccion->TRN_Tipo == 'E' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $mov->transaccion->TRN_Nombre }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 dark:text-gray-300">
                                        @if ($mov->ORC_Numero)
                                            <span class="text-xs font-semibold">OC:</span> {{ $mov->ORC_Numero }}
                                        @else
                                            <span class="text-xs font-semibold">Item:</span>
                                            {{ $mov->producto->PRO_Nombre ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td
                                        class="px-6 py-4 text-center font-bold 
    {{ $mov->BOD_cantidad == 0 ? 'text-gray-500' : ($mov->transaccion->TRN_Tipo == 'E' ? 'text-green-600' : 'text-red-600') }}">
                                        {{ $mov->BOD_cantidad }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-3">
                                        <a href="{{ route('warehouse.show', $mov->BOD_Codigo) }}"
                                            class="text-blue-600 hover:text-blue-900 inline-block" title="Ver Detalle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <button onclick="openDeleteModal('{{ $mov->BOD_Codigo }}')"
                                            class="text-red-600 hover:text-red-900 inline-block" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        No se encontraron movimientos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($movimientos->hasPages())
                    <div class="px-6 py-4 border-t dark:border-zinc-700">
                        {{ $movimientos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="delete-form" method="POST" class="p-6">
                    @csrf
                    @method('DELETE')
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Eliminar Registro de Kardex</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        ¿Estás seguro de eliminar este registro? <br>
                        <span class="text-red-500 font-bold font-xs uppercase underline">Nota: Esto no revertirá
                            automáticamente el stock de los productos.</span>
                    </p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 rounded-md text-sm font-semibold">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-semibold hover:bg-red-700 shadow-sm">Eliminar
                            Permanente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(codigo) {
            const form = document.getElementById('delete-form');
            form.action = `/admin/bodegas/${codigo}`; // Ajusta el prefijo según tu ruta
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }
    </script>
@endsection
