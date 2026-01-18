@extends('components.admin-layout')

@section('page-title', 'Editar Cliente')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Editar Cliente
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Actualice la información del cliente
                        </p>
                    </div>
                    <a href="{{ route('customers.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:hover:bg-zinc-600">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
                <form action="{{ route('customers.update', $cliente->CLI_Ced_Ruc) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Información Básica -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Información Básica
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Cédula/RUC (solo lectura) -->
                                <div>
                                    <label for="CLI_Ced_Ruc"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Cédula/RUC *
                                    </label>
                                    <input type="text" name="CLI_Ced_Ruc" id="CLI_Ced_Ruc"
                                        value="{{ old('CLI_Ced_Ruc', $cliente->CLI_Ced_Ruc) }}" readonly
                                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm dark:bg-zinc-600 dark:border-zinc-600 dark:text-white sm:text-sm">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        La cédula/RUC no se puede modificar
                                    </p>
                                </div>

                                <!-- Nombre -->
                                <div>
                                    <label for="CLI_Nombre"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nombre del Cliente *
                                    </label>
                                    <input type="text" name="CLI_Nombre" id="CLI_Nombre"
                                        value="{{ old('CLI_Nombre', $cliente->CLI_Nombre) }}" placeholder="Ej: Juan Pérez"
                                        onkeypress="return soloLetras(event)"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                        required>
                                    @error('CLI_Nombre')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Información de Contacto
                            </h3>

                            <div class="grid grid-cols-1 gap-6">
                                <!-- Dirección -->
                                <div>
                                    <label for="CLI_Direccion"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Dirección *
                                    </label>
                                    <input type="text" name="CLI_Direccion" id="CLI_Direccion"
                                        value="{{ old('CLI_Direccion', $cliente->CLI_Direccion) }}" required maxlength="150"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm @error('CLI_Direccion') border-red-500 @enderror"
                                        placeholder="Dirección completa">
                                    @error('CLI_Direccion')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Teléfono -->
                                    <div>
                                        <label for="CLI_Telefono"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Teléfono *
                                        </label>
                                        <input type="tel" name="CLI_Telefono" id="CLI_Telefono"
                                            value="{{ old('CLI_Telefono', $cliente->CLI_Telefono) }}" required
                                            maxlength="10"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm @error('CLI_Telefono') border-red-500 @enderror"
                                            placeholder="Ej: 0987654321">
                                        @error('CLI_Telefono')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Correo -->
                                    <div>
                                        <label for="CLI_Correo"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Correo Electrónico *
                                        </label>
                                        <input type="email" name="CLI_Correo" id="CLI_Correo"
                                            value="{{ old('CLI_Correo', $cliente->CLI_Correo) }}" required maxlength="60"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm @error('CLI_Correo') border-red-500 @enderror"
                                            placeholder="correo@ejemplo.com">
                                        @error('CLI_Correo')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('customers.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:hover:bg-zinc-600">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Actualizar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script de validación -->
    <script>
        function soloLetras(e) {
            let key = e.keyCode || e.which;
            let tecla = String.fromCharCode(key).toLowerCase();
            let letras = " abcdefghijklmnñopqrstuvwxyz";
            let especiales = [8, 37, 39, 46];

            if (!letras.includes(tecla) && !especiales.includes(key)) {
                return false;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const telefonoInput = document.getElementById('CLI_Telefono');

            telefonoInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            document.querySelector('form').addEventListener('submit', function(e) {
                if (telefonoInput.value.length !== 10) {
                    alert('El teléfono debe tener 10 dígitos');
                    telefonoInput.focus();
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
