@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-brand">Mis Pedidos / Facturas</h1>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if ($facturas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6">Nº Factura</th>
                                <th class="py-3 px-6">Fecha</th>
                                <th class="py-3 px-6 text-right">Total</th>
                                <th class="py-3 px-6 text-center">Estado</th>
                                <th class="py-3 px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach ($facturas as $factura)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 font-bold whitespace-nowrap">
                                        {{ $factura->FAC_Codigo }}
                                    </td>
                                    <td class="py-3 px-6">
                                        {{ $factura->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-6 text-right font-bold text-gray-800">
                                        ${{ number_format($factura->FAC_Total, 2) }}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <span
                                            class="bg-{{ $factura->FAC_Estado == 'PAGADA' ? 'green' : ($factura->FAC_Estado == 'PENDIENTE' ? 'yellow' : 'red') }}-200 text-{{ $factura->FAC_Estado == 'PAGADA' ? 'green' : ($factura->FAC_Estado == 'PENDIENTE' ? 'yellow' : 'red') }}-800 py-1 px-3 rounded-full text-xs">
                                            {{ $factura->FAC_Estado }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="#" class="text-brand hover:text-red-800 font-bold">Ver Detalle</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $facturas->links() }}
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
