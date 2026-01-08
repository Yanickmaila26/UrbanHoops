@extends('components.admin-layout')

@section('page-title', 'Detalle de Orden #' . $orden->ORC_Numero)

@section('content')
    <div class="max-w-4xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6 no-print">
            <a href="{{ route('purchase-orders.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7" />
                </svg>
                Volver al listado
            </a>
            <button onclick="window.print()"
                class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Imprimir PDF
            </button>
        </div>

        <div class="bg-white dark:bg-zinc-800 shadow-lg rounded-lg overflow-hidden border dark:border-zinc-700">
            <div class="p-8 border-b dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900/50">
                <div class="flex justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-blue-600 uppercase">Orden de Compra</h1>
                        <p class="text-gray-500 mt-1">Número: <span
                                class="font-mono font-bold text-gray-800 dark:text-white">{{ $orden->ORC_Numero }}</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <span
                            class="px-3 py-1 rounded-full text-sm font-bold {{ $orden->ORC_Estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $orden->ORC_Estado ? 'ESTADO: ACTIVA' : 'ESTADO: CERRADA' }}
                        </span>
                        <p class="text-sm text-gray-500 mt-2">Emisión:
                            {{ \Carbon\Carbon::parse($orden->ORC_Fecha_Emision)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 p-8 border-b dark:border-zinc-700">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Datos del Proveedor</h3>
                    <p class="text-lg font-bold dark:text-white">{{ $orden->proveedor->PRV_Nombre }}</p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">RUC: {{ $orden->PRV_Ced_Ruc }}</p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Telf: {{ $orden->proveedor->PRV_Telefono }}</p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $orden->proveedor->PRV_Correo }}</p>
                </div>
                <div class="text-right">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Entrega y Logística</h3>
                    <p class="text-sm font-bold dark:text-white">Fecha Estimada: <span
                            class="font-normal">{{ \Carbon\Carbon::parse($orden->ORC_Fecha_Entrega)->format('d/m/Y') }}</span>
                    </p>
                </div>
            </div>

            <div class="p-8">
                <table class="w-full mb-8">
                    <thead>
                        <tr class="text-left border-b-2 dark:border-zinc-700">
                            <th class="py-3 font-bold text-gray-700 dark:text-gray-300">Cód. Producto</th>
                            <th class="py-3 font-bold text-gray-700 dark:text-gray-300">Descripción</th>
                            <th class="py-3 text-center font-bold text-gray-700 dark:text-gray-300">Cantidad</th>
                            <th class="py-3 text-right font-bold text-gray-700 dark:text-gray-300">P. Unitario</th>
                            <th class="py-3 text-right font-bold text-gray-700 dark:text-gray-300">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orden->productos as $producto)
                            <tr class="border-b dark:border-zinc-700">
                                <td class="py-4 text-sm font-mono text-gray-600 dark:text-gray-400">
                                    {{ $producto->PRO_Codigo }}</td>
                                <td class="py-4">
                                    <span class="font-bold dark:text-white">{{ $producto->PRO_Nombre }}</span><br>
                                    <span class="text-xs text-gray-500">{{ $producto->PRO_Marca }} |
                                        {{ $producto->PRO_Color }} | Talla: {{ $producto->PRO_Talla }}</span>
                                </td>
                                <td class="py-4 text-center dark:text-white">{{ $producto->pivot->cantidad_solicitada }}
                                </td>
                                <td class="py-4 text-right dark:text-white">${{ number_format($producto->PRO_Precio, 2) }}
                                </td>
                                <td class="py-4 text-right font-bold dark:text-white">
                                    ${{ number_format($producto->pivot->cantidad_solicitada * $producto->PRO_Precio, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-end">
                    <div class="w-full md:w-64 bg-gray-50 dark:bg-zinc-900 p-4 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-500">Subtotal:</span>
                            <span class="dark:text-white">${{ number_format($orden->ORC_Monto_Total / 1.15, 2) }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-500">IVA (15%):</span>
                            <span
                                class="dark:text-white">${{ number_format($orden->ORC_Monto_Total - $orden->ORC_Monto_Total / 1.15, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t dark:border-zinc-700 pt-2 font-bold text-lg">
                            <span class="text-blue-600">TOTAL:</span>
                            <span class="text-blue-600">${{ number_format($orden->ORC_Monto_Total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-gray-50 dark:bg-zinc-900/50 text-center text-xs text-gray-400">
                <p>Esta es una orden de compra oficial generada por el sistema de inventario.</p>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .shadow-lg {
                shadow: none;
                border: 1px solid #eee;
            }
        }
    </style>
@endsection
