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
                    {{ $pedido->PED_Fecha->format('d/m/Y H:i A') }}</p>
                <p class="text-gray-600 mt-2 text-xl"><span class="font-semibold">Total Pagado:</span>
                    ${{ number_format($pedido->factura->FAC_Total ?? 0, 2) }}</p>
            </div>

            <div class="flex justify-center gap-4">
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
