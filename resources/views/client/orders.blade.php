@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-brand">Mis Pedidos / Facturas</h1>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if ($pedidos->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-100 border-b border-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6">Nº Pedido</th>
                                    <th class="py-3 px-6">Fecha</th>
                                    <th class="py-3 px-6 text-right">Total</th>
                                    <th class="py-3 px-6 text-center">Estado</th>
                                    <th class="py-3 px-6 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @foreach ($pedidos as $pedido)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6 font-bold whitespace-nowrap">
                                            #{{ $pedido->PED_Codigo }}
                                        </td>
                                        <td class="py-3 px-6">
                                            {{ $pedido->PED_Fecha->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="py-3 px-6 text-right font-bold text-gray-800">
                                            ${{ number_format($pedido->factura->FAC_Total ?? 0, 2) }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            @php
                                                $estadoColor = match ($pedido->PED_Estado) {
                                                    'Pendiente' => 'yellow',
                                                    'Procesando' => 'blue',
                                                    'Enviado' => 'purple',
                                                    'Entregado' => 'green',
                                                    default => 'gray',
                                                };
                                            @endphp
                                            <span
                                                class="bg-{{ $estadoColor }}-100 text-{{ $estadoColor }}-800 py-1 px-3 rounded-full text-xs font-bold">
                                                {{ $pedido->PED_Estado }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <a href="{{ route('client.checkout.success', $pedido->PED_Codigo) }}"
                                                class="text-brand hover:text-red-800 font-bold">Ver Detalles</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4">
                        {{ $pedidos->links() }}
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <p class="text-xl mb-4">No has realizado ningún pedido aún.</p>
                        <a href="{{ route('productos-servicios') }}" class="btn btn-brand">Ir a comprar</a>
                    </div>
                @endif
            </div>
        </div>
    @endsection
