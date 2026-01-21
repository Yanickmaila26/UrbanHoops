@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-brand">Carrito de Compras</h1>

        @if ($carrito && $carrito->detalles->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        @foreach ($carrito->detalles as $detalle)
                            <div class="flex items-center p-4 border-b border-gray-200 last:border-b-0">
                                <!-- Image -->
                                <div class="w-20 h-20 flex-shrink-0 bg-gray-100 rounded overflow-hidden">
                                    <!-- Reusing product image logic, assuming generic for now or specific field -->
                                    <img src="https://via.placeholder.com/80" alt="{{ $detalle->producto->PRO_Nombre }}"
                                        class="w-full h-full object-cover">
                                </div>
                                <!-- Info -->
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-bold text-gray-800">{{ $detalle->producto->PRO_Nombre }}</h3>
                                    <p class="text-sm text-gray-500">{{ $detalle->producto->PRO_Descripcion }}</p>
                                </div>
                                <!-- Qty & Price -->
                                <div class="flex items-center gap-4">
                                    <span class="font-bold text-gray-600">{{ $detalle->DCA_Cantidad }} x
                                        ${{ number_format($detalle->DCA_PrecioUnitario, 2) }}</span>
                                    <span
                                        class="font-bold text-lg text-brand">${{ number_format($detalle->DCA_Total, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold mb-4">Resumen del Pedido</h2>
                        <div class="flex justify-between mb-2 text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($carrito->getTotal(), 2) }}</span>
                        </div>
                        <div class="flex justify-between mb-4 text-gray-600">
                            <span>Envío</span>
                            <span>Calculado en checkout</span>
                        </div>
                        <div class="border-t pt-4 flex justify-between font-bold text-xl mb-6">
                            <span>Total</span>
                            <span>${{ number_format($carrito->getTotal(), 2) }}</span>
                        </div>
                        <button class="w-full btn btn-brand py-3 text-lg">Proceder al Pago</button>
                        <a href="{{ route('productos-servicios') }}"
                            class="block text-center mt-4 text-gray-500 hover:text-gray-800">Seguir comprando</a>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-lg shadow">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Tu carrito está vacío</h2>
                <p class="text-gray-500 mb-6">¡Explora nuestros productos y encuentra lo que buscas!</p>
                <a href="{{ route('productos-servicios') }}" class="btn btn-brand">Ver Productos</a>
            </div>
        @endif
    </div>
@endsection
