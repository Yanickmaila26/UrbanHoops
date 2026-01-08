@extends('components.admin-layout')

@section('page-title', 'Editar Proveedor')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Editar Proveedor
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Actualice la información del proveedor
                        </p>
                    </div>
                    <a href="{{ route('suppliers.index') }}"
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
                <form action="{{ route('suppliers.update', $supplier->PRV_Ced_Ruc) }}" method="POST" class="p-6">
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
                                    <label for="PRV_Ced_Ruc"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Cédula/RUC *
                                    </label>
                                    <input type="text" name="PRV_Ced_Ruc" id="PRV_Ced_Ruc"
                                        value="{{ old('PRV_Ced_Ruc', $supplier->PRV_Ced_Ruc) }}" readonly
                                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm dark:bg-zinc-600 dark:border-zinc-600 dark:text-white sm:text-sm"
                                        placeholder="Ej: 1234567890001">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        La cédula/RUC no se puede modificar
                                    </p>
                                </div>

                                <!-- Nombre -->
                                <div>
                                    <label for="PRV_Nombre"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del
                                        Proveedor</label>
                                    <input type="text" name="PRV_Nombre" id="PRV_Nombre"
                                        value="{{ old('PRV_Nombre', $supplier->PRV_Nombre ?? '') }}"
                                        placeholder="Ej: Distribuidora Textil S.A." onkeypress="return soloLetras(event)"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                        required>
                                    @error('PRV_Nombre')
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
                                    <label for="PRV_Direccion"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Dirección *
                                    </label>
                                    <input type="text" name="PRV_Direccion" id="PRV_Direccion"
                                        value="{{ old('PRV_Direccion', $supplier->PRV_Direccion) }}" required
                                        maxlength="150"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm @error('PRV_Direccion') border-red-500 @enderror"
                                        placeholder="Dirección completa">
                                    @error('PRV_Direccion')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Teléfono -->
                                    <div>
                                        <label for="PRV_Telefono"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Teléfono *
                                        </label>
                                        <input type="tel" name="PRV_Telefono" id="PRV_Telefono"
                                            value="{{ old('PRV_Telefono', $supplier->PRV_Telefono) }}" required
                                            maxlength="10"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm @error('PRV_Telefono') border-red-500 @enderror"
                                            placeholder="Ej: 0987654321">
                                        @error('PRV_Telefono')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Correo -->
                                    <div>
                                        <label for="PRV_Correo"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Correo Electrónico *
                                        </label>
                                        <input type="email" name="PRV_Correo" id="PRV_Correo"
                                            value="{{ old('PRV_Correo', $supplier->PRV_Correo) }}" required maxlength="60"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm @error('PRV_Correo') border-red-500 @enderror"
                                            placeholder="correo@ejemplo.com">
                                        @error('PRV_Correo')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('suppliers.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:hover:bg-zinc-600">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Actualizar Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script para validación -->
    <script>
        function soloLetras(e) {
            // Capturar la tecla presionada
            let key = e.keyCode || e.which;
            let tecla = String.fromCharCode(key).toLowerCase();

            // Definir caracteres permitidos: letras, espacio, tildes y eñe
            let letras = " abcdefghijklmnñopqrstuvwxyz";

            // Permitir teclas de control (Backspace, flechas, etc.)
            let especiales = [8, 37, 39, 46];
            let tecla_especial = false;

            for (let i in especiales) {
                if (key == especiales[i]) {
                    tecla_especial = true;
                    break;
                }
            }

            // Si no es letra ni tecla especial, bloquear la entrada
            if (letras.indexOf(tecla) == -1 && !tecla_especial) {
                return false;
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const telefonoInput = document.getElementById('PRV_Telefono');

            // Validar solo números para teléfono
            telefonoInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Validar formulario antes de enviar
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                let valid = true;

                // Validar teléfono (10 dígitos)
                if (telefonoInput.value.length !== 10) {
                    alert('El teléfono debe tener 10 dígitos');
                    telefonoInput.focus();
                    valid = false;
                }

                if (!valid) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection
