@extends('components.admin-layout')

@section('page-title', 'Registrar Movimiento de Bodega')

@section('content')
    <div class="max-w-5xl mx-auto p-6">
        <div class="bg-white dark:bg-zinc-800 shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 border-b dark:border-zinc-700">
                <h2 class="text-xl font-bold dark:text-white uppercase">Nuevo Registro en Kardex</h2>
            </div>

            <form action="{{ route('kardex.store') }}" method="POST" id="kardex-form" class="p-6" x-data="{ loading: false, validationError: '' }"
                @submit.prevent="validateAndSubmit">
                @csrf

                <!-- Error Banner -->
                <div x-show="validationError" x-transition
                    class="bg-red-50 border-l-4 border-red-400 p-4 mb-4 rounded-r dark:bg-red-900/20 dark:border-red-600">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm font-medium text-red-800 dark:text-red-200" x-text="validationError"></p>
                        <button @click="validationError = ''" type="button"
                            class="ml-auto text-red-500 hover:text-red-700">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase">Código
                            Kardex</label>
                        <input type="text" name="KAR_Codigo" value="{{ $nuevoCodigo }}" readonly
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 dark:bg-zinc-900 dark:text-white font-mono font-bold">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase">Bodega</label>
                        <select name="BOD_Codigo"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                            @foreach ($bodegas as $bodega)
                                <option value="{{ $bodega->BOD_Codigo }}">{{ $bodega->BOD_Nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase">Tipo de
                            Transacción</label>
                        <select name="TRN_Codigo" id="TRN_Codigo" onchange="checkTransactionType()"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                            <option value="">Seleccione...</option>
                            @foreach ($transacciones as $trn)
                                <option value="{{ $trn->TRN_Codigo }}" data-tipo="{{ $trn->TRN_Tipo }}">
                                    {{ $trn->TRN_Nombre }} ({{ $trn->TRN_Tipo }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase">Origen del
                            Datos</label>
                        <div class="mt-2 flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="modo" value="oc" checked onclick="toggleModo('oc')"
                                    class="text-blue-600">
                                <span class="ml-2 text-sm dark:text-white">Orden Compra</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="modo" value="manual" onclick="toggleModo('manual')"
                                    class="text-blue-600">
                                <span class="ml-2 text-sm dark:text-white">Ajuste Manual</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div id="section-oc" class="mb-8 p-4 bg-gray-50 dark:bg-zinc-900/50 rounded-lg border dark:border-zinc-700">
                    <label class="block text-sm font-bold dark:text-gray-300 mb-2 uppercase">Seleccionar Orden de Compra
                        Activa</label>
                    <select name="ORC_Numero" id="ORC_Numero"
                        class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white">
                        <option value="">--- Buscar Orden ---</option>
                        @foreach ($ordenes as $oc)
                            <option value="{{ $oc->ORC_Numero }}">{{ $oc->ORC_Numero }} - Proveedor:
                                {{ $oc->proveedor->PRV_Nombre }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500 italic">* Al procesar una OC, el sistema afectará el stock de todos
                        los productos incluidos en ella automáticamente.</p>
                </div>

                <div id="section-manual" class="hidden mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold dark:text-white uppercase text-sm">Productos para Ajuste Individual</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Producto</label>
                            <select name="PRO_Codigo" id="PRO_Codigo"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white">
                                <option value="">Seleccione un producto...</option>
                                @foreach ($productos as $prod)
                                    <option value="{{ $prod->PRO_Codigo }}">[{{ $prod->PRO_Codigo }}]
                                        {{ $prod->PRO_Nombre }} - Stock: {{ $prod->PRO_Stock }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Cantidad</label>
                            <input type="number" name="KAR_cantidad" id="KAR_cantidad" min="1"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white"
                                placeholder="0">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t dark:border-zinc-700 pt-6">
                    <a href="{{ route('warehouse.index') }}"
                        class="px-6 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-700 dark:text-white rounded-md hover:bg-gray-300 transition uppercase font-bold text-sm">
                        Cancelar
                    </a>
                    <button type="submit" :disabled="loading"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-lg transition uppercase font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!loading" class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Procesar Movimiento
                        </span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Procesando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModo(modo) {
            const sectionOc = document.getElementById('section-oc');
            const sectionManual = document.getElementById('section-manual');
            const inputOc = document.getElementById('ORC_Numero');
            const inputProd = document.getElementById('PRO_Codigo');
            const inputCant = document.getElementById('KAR_cantidad');

            if (modo === 'oc') {
                sectionOc.classList.remove('hidden');
                sectionManual.classList.add('hidden');
                inputProd.value = "";
                inputCant.value = "";
                inputOc.required = true;
                inputProd.required = false;
            } else {
                sectionOc.classList.add('hidden');
                sectionManual.classList.remove('hidden');
                inputOc.value = "";
                inputOc.required = false;
                inputProd.required = true;
            }
        }

        // Validar tipo T07 (Cancelado) - debe ser por OC
        function checkTransactionType() {
            const selectTrn = document.getElementById('TRN_Codigo');
            if (selectTrn.value === 'T07') {
                const form = document.getElementById('kardex-form');
                const alpineData = Alpine.$data(form);
                alpineData.validationError =
                    'Las cancelaciones (T07) deben estar asociadas obligatoriamente a una Orden de Compra.';
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                document.querySelector('input[value="oc"]').checked = true;
                toggleModo('oc');
            }
        }

        // Función de validación integrada con Alpine.js
        function validateAndSubmit() {
            const form = document.getElementById('kardex-form');
            // Fix: Access Alpine data safely
            const alpineData = Alpine.$data(form);
            const modo = document.querySelector('input[name="modo"]:checked').value;
            const trn = document.getElementById('TRN_Codigo').value;
            const ocNumero = document.getElementById('ORC_Numero').value;
            const proCodig = document.getElementById('PRO_Codigo').value;
            const cantidad = document.getElementById('KAR_cantidad').value;

            // Validar tipo de transacción
            if (!trn) {
                alpineData.validationError = 'Por favor, seleccione un tipo de transacción.';
                alpineData.loading = false;
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                return false;
            }

            // Validar modo Orden de Compra
            if (modo === 'oc' && !ocNumero) {
                alpineData.validationError = 'Debe seleccionar una Orden de Compra.';
                alpineData.loading = false;
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                return false;
            }

            // Validar modo Manual
            if (modo === 'manual' && (!proCodig || !cantidad)) {
                alpineData.validationError = 'Debe completar el producto y la cantidad para el ajuste manual.';
                alpineData.loading = false;
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                return false;
            }

            // Si todas las validaciones pasan
            alpineData.validationError = '';
            alpineData.loading = true;
            form.submit();
        }
    </script>
@endsection
