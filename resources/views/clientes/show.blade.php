@extends('components.admin-layout')

@section('page-title', 'Detalles del Cliente')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Detalles del Cliente
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Información completa del cliente
                        </p>
                    </div>

                    <div class="flex space-x-2">
                        <a href="{{ route('customers.edit', $cliente->CLI_Ced_Ruc) }}"
                           class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md text-xs font-semibold uppercase hover:bg-yellow-700">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar
                        </a>

                        <a href="{{ route('customers.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-xs font-semibold uppercase dark:bg-zinc-700 dark:text-white hover:bg-gray-300 dark:hover:bg-zinc-600">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Información Principal -->
                <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Información Principal
                        </h3>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Cédula/RUC
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $cliente->CLI_Ced_Ruc }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Nombre
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $cliente->CLI_Nombre }}
                                </dd>
                            </div>

                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Dirección
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $cliente->CLI_Direccion }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Teléfono
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $cliente->CLI_Telefono }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Correo Electrónico
                                </dt>
                                <dd class="mt-1 text-sm">
                                    <a href="mailto:{{ $cliente->CLI_Correo }}"
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $cliente->CLI_Correo }}
                                    </a>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Información Adicional
                        </h3>

                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Fecha de Creación
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $cliente->created_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Última Actualización
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $cliente->updated_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Botones inferiores -->
            <div class="mt-6 flex justify-between">
                <a href="{{ route('customers.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-xs font-semibold uppercase dark:bg-zinc-700 dark:text-white hover:bg-gray-300 dark:hover:bg-zinc-600">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver a la lista
                </a>

                <a href="{{ route('customers.edit', $cliente->CLI_Ced_Ruc) }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md text-xs font-semibold uppercase hover:bg-yellow-700">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar Cliente
                </a>
            </div>

        </div>
    </div>
@endsection
