@extends('layouts.app')

@section('title', 'Pedido Recibido')

@section('content')
    <section class="container py-16 text-center">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6 text-green-500">
                <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h1 class="text-4xl font-bold text-gray-900 mb-4 font-poppins">¡Gracias por tu compra!</h1>
            <p class="text-lg text-gray-600 mb-8">
                Tu pedido <span class="font-bold text-brand">#{{ $pedido->PED_Codigo }}</span> ha sido recibido exitosamente
                y ya está en proceso de envío.
            </p>

            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left border border-gray-100 shadow-inner">
                <h3 class="font-bold text-gray-800 mb-2 border-b pb-2">Detalles del Envío</h3>
                <p class="text-gray-600 mb-1"><span class="font-semibold">Destinatario:</span>
                    {{ $pedido->cliente->CLI_Nombre }}</p>
                <p class="text-gray-600 mb-1"><span class="font-semibold">Dirección:</span>
                    {{ $pedido->datosFacturacion->DAF_Direccion }}, {{ $pedido->datosFacturacion->DAF_Ciudad }}</p>
                <p class="text-gray-600 mb-1"><span class="font-semibold">Fecha:</span>
                    {{ \Carbon\Carbon::parse($pedido->PED_Fecha)->format('d/m/Y H:i A') }}</p>
                <p class="text-gray-600 mt-2 text-xl"><span class="font-semibold">Total Pagado:</span>
                    ${{ number_format($pedido->factura->FAC_Total ?? 0, 2) }}</p>
            </div>

            <!-- Product Details -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-8 overflow-hidden text-left">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">Productos Comprados</h3>
                </div>
                <div class="p-6">
                    @foreach ($productos as $producto)
                        <div
                            class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">
                            <img src="{{ $producto->PRO_Imagen ? asset('storage/' . $producto->PRO_Imagen) : asset('images/default.jpg') }}"
                                alt="{{ $producto->PRO_Nombre }}" class="w-16 h-16 object-cover rounded bg-gray-100">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-sm">{{ $producto->PRO_Nombre }}</h4>
                                <p class="text-xs text-gray-500">
                                    Talla:
                                    {{ $producto->pivot->DFC_TALLA ?? ($producto->pivot->dfc_talla ?? $producto->pivot->DFC_Talla) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">
                                    ${{ number_format($producto->pivot->DFC_PRECIO ?? ($producto->pivot->dfc_precio ?? $producto->pivot->DFC_Precio), 2) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    x{{ $producto->pivot->DFC_CANTIDAD ?? ($producto->pivot->dfc_cantidad ?? $producto->pivot->DFC_Cantidad) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-center gap-4">
                <a href="{{ route('client.checkout.invoice', $pedido->PED_Codigo) }}"
                    class="btn btn-success px-6 py-2 font-bold" target="_blank">
                    📄 Descargar Factura PDF
                </a>
                <a href="{{ route('client.orders') }}" class="btn btn-outline-dark px-6 py-2">
                    Ver Mis Pedidos
                </a>
                <a href="{{ route('productos-servicios') }}"
                    class="btn btn-brand px-6 py-2 font-bold uppercase tracking-wider">
                    Seguir Comprando
                </a>
            </div>
        </div>
    </section>
@endsection
