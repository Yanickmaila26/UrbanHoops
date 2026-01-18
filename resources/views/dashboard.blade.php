@extends('components.admin-layout')

@section('page-title', 'Panel de Control')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('suppliers.index') }}"
                    class="group bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-md border border-transparent hover:border-blue-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-blue-500 uppercase tracking-wider">Gestión</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-blue-500 transition-colors">
                        Proveedores</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Administración de contactos y RUC.</p>
                </a>

                <a href="{{ route('products.index') }}"
                    class="group bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-md border border-transparent hover:border-yellow-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg text-yellow-600 dark:text-yellow-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-yellow-500 uppercase tracking-wider">Catálogo</span>
                    </div>
                    <h3
                        class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-yellow-500 transition-colors">
                        Productos</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Control de inventario, tallas y marcas.</p>
                </a>

                <a href="{{ route('purchase-orders.index') }}"
                    class="group bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-md border border-transparent hover:border-green-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg text-green-600 dark:text-green-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-green-500 uppercase tracking-wider">Compras</span>
                    </div>
                    <h3
                        class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-green-500 transition-colors">
                        Órdenes</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Generación de pedidos y fechas de entrega.</p>
                </a>

                <a href="#"
                    class="group bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-md border border-transparent hover:border-purple-500 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg text-purple-600 dark:text-purple-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-purple-500 uppercase tracking-wider">Stock</span>
                    </div>
                    <h3
                        class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-purple-500 transition-colors">
                        Bodega</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Ubicaciones y movimientos de mercancía.</p>
                </a>

            </div>
        </div>
    </div>

    <!-- Botón Flotante de Ayuda (H10) -->
    <div x-data="{ open: false }" class="fixed bottom-6 right-6">
        <div x-show="open" x-transition class="absolute bottom-16 right-0 w-64 bg-white dark:bg-zinc-700 rounded-lg shadow-xl p-4 border dark:border-zinc-600 mb-2">
            <h4 class="font-bold text-gray-800 dark:text-white mb-2">¿Necesitas ayuda?</h4>
            <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-2">
                <li><a href="#" class="hover:text-blue-500">• Manual de Usuario</a></li>
                <li><a href="#" class="hover:text-blue-500">• Preguntas Frecuentes</a></li>
                <li><a href="#" class="hover:text-blue-500">• Contactar Soporte</a></li>
            </ul>
        </div>
        <button @click="open = !open" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-3 shadow-lg transition-transform transform hover:scale-110">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>
@endsection
