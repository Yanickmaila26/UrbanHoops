@extends('components.admin-layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold dark:text-white">Factura: {{ $factura->FAC_Codigo }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('invoices.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 dark:bg-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-600">
                    Volver
                </a>
                @if ($factura->FAC_Estado !== 'Anu')
                    <form action="{{ route('invoices.destroy', $factura->FAC_Codigo) }}" method="POST"
                        onsubmit="return confirm('¿Está seguro de anular esta factura?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Anular
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Info Header -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Client Info -->
            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-4 border-b pb-2 border-gray-200 dark:border-zinc-700">
                    Cliente</h3>
                <div class="space-y-2">
                    <p class="text-sm dark:text-gray-300"><span class="font-bold text-gray-700 dark:text-gray-400">Razón
                            Social:</span> {{ $factura->cliente->CLI_Nombre }} {{ $factura->cliente->CLI_Apellido }}</p>
                    <p class="text-sm dark:text-gray-300"><span class="font-bold text-gray-700 dark:text-gray-400">Cédula /
                            RUC:</span> {{ $factura->cliente->CLI_Ced_Ruc }}</p>
                    <p class="text-sm dark:text-gray-300"><span
                            class="font-bold text-gray-700 dark:text-gray-400">Dirección:</span>
                        {{ $factura->cliente->CLI_Direccion ?? 'N/A' }}</p>
                    <p class="text-sm dark:text-gray-300"><span
                            class="font-bold text-gray-700 dark:text-gray-400">Teléfono:</span>
                        {{ $factura->cliente->CLI_Telefono ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Invoice Info -->
            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-4 border-b pb-2 border-gray-200 dark:border-zinc-700">
                    Detalles de Factura</h3>
                <div class="space-y-2">
                    <p class="text-sm dark:text-gray-300"><span
                            class="font-bold text-gray-700 dark:text-gray-400">Código:</span> {{ $factura->FAC_Codigo }}</p>
                    <p class="text-sm dark:text-gray-300"><span class="font-bold text-gray-700 dark:text-gray-400">Fecha
                            Emisión:</span> {{ $factura->created_at->format('d/m/Y H:i:s') }}</p>
                    <p class="text-sm dark:text-gray-300 flex items-center gap-2">
                        <span class="font-bold text-gray-700 dark:text-gray-400">Estado:</span>
                        @php
                            $statusColors = [
                                'Pen' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
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
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$factura->FAC_Estado] }}">
                            {{ $statusLabels[$factura->FAC_Estado] }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="bg-white dark:bg-zinc-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">Productos</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Código</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Descripción</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Precio Unit.</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Cantidad</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach ($factura->productos as $producto)
                            @php
                                $price = $producto->pivot->DFC_Precio;
                                $qty = $producto->pivot->DFC_Cantidad;
                                $subtotal = $price * $qty;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $producto->PRO_Codigo }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $producto->PRO_Nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                    ${{ number_format($price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                    {{ $qty }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($subtotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <td colspan="4"
                                class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900 dark:text-white uppercase">
                                Total Factura</td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-xl font-bold text-right text-gray-900 dark:text-white">
                                ${{ number_format($factura->FAC_Total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
