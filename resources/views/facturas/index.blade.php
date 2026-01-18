@extends('components.admin-layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold dark:text-white">Facturas</h2>
            <a href="{{ route('invoices.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                + Nueva Factura
            </a>
        </div>

        <!-- Search -->
        <div class="mb-6 bg-white dark:bg-zinc-800 p-4 rounded-lg shadow">
            <form action="{{ route('invoices.index') }}" method="GET" class="flex gap-4">
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Buscar por Código, Cliente o Cédula..."
                    class="flex-1 rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
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
                        <th class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-300">Cliente</th>
                        <th class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-300">Fecha</th>
                        <th class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-300">Total</th>
                        <th class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-300">Estado</th>
                        <th class="px-6 py-3 text-sm font-medium text-gray-500 dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($facturas as $factura)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition">
                            <td class="px-6 py-4 dark:text-gray-300 font-mono text-sm">{{ $factura->FAC_Codigo }}</td>
                            <td class="px-6 py-4 dark:text-gray-300">
                                {{ $factura->cliente->CLI_Nombre }} {{ $factura->cliente->CLI_Apellido }}
                                <div class="text-xs text-gray-500">{{ $factura->CLI_Ced_Ruc }}</div>
                            </td>
                            <td class="px-6 py-4 dark:text-gray-300 text-sm">{{ $factura->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 dark:text-gray-300 font-bold">${{ number_format($factura->FAC_Total, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Pen' =>
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'Pag' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        'Anu' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                    $statusLabels = [
                                        'Pen' => 'Pendiente',
                                        'Pag' => 'Pagada',
                                        'Anu' => 'Anulada',
                                    ];
                                @endphp
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$factura->FAC_Estado] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$factura->FAC_Estado] ?? $factura->FAC_Estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 flex gap-2">
                                <a href="{{ route('invoices.show', $factura->FAC_Codigo) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="Ver Detalle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                @if ($factura->FAC_Estado !== 'Anu')
                                    <form action="{{ route('invoices.destroy', $factura->FAC_Codigo) }}" method="POST"
                                        onsubmit="return confirm('¿Está seguro de anular esta factura?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            title="Anular Factura">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No se
                                encontraron facturas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $facturas->links() }}
        </div>
    </div>
@endsection
