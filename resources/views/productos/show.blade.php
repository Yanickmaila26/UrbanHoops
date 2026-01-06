@extends('components.admin-layout')

@section('page-title', 'Detalles del Producto')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $producto->PRO_Nombre }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Código único: <span class="font-mono font-bold">{{ $producto->PRO_Codigo }}</span>
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('productos.edit', $producto->PRO_Codigo) }}"
                           class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar
                        </a>
                        <a href="{{ route('productos.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider">
                            Imagen del Producto
                        </h3>
                        <div class="aspect-square w-full relative overflow-hidden rounded-lg border dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900">
                            @if($producto->PRO_Imagen)
                                <img src="{{ asset('storage/' . $producto->PRO_Imagen) }}"
                                     alt="{{ $producto->PRO_Nombre }}"
                                     class="object-cover w-full h-full hover:scale-105 transition duration-300">
                            @else
                                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                                    <svg class="h-16 w-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs italic">Sin imagen disponible</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Detalles Técnicos
                            </h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Código</dt>
                                    <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $producto->PRO_Codigo }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $producto->PRO_Nombre }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción Corta</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white italic">
                                        "{{ $producto->PRO_Descripcion_Corta }}"
                                    </dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción Detallada</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-zinc-900 p-4 rounded-md border dark:border-zinc-700 whitespace-pre-line">
                                        {{ $producto->PRO_Descripcion_Larga }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado el</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $producto->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última edición</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $producto->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
