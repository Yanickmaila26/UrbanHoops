@extends('components.admin-layout')

@section('page-title', 'Registrar Movimiento de Bodega')

@section('content')
    <div class="max-w-5xl mx-auto p-6">
        <div class="bg-white dark:bg-zinc-800 shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 border-b dark:border-zinc-700">
                <h2 class="text-xl font-bold dark:text-white uppercase">Nuevo Registro en Kardex</h2>
            </div>

            <form action="{{ route('kardex.store') }}" method="POST" id="kardex-form" class="p-6">
                @csrf

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
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-lg transition uppercase font-bold text-sm">
                        Procesar Movimiento
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
                // Limpiamos manual
                inputProd.value = "";
                inputCant.value = "";
                inputOc.required = true;
                inputProd.required = false;
            } else {
                sectionOc.classList.add('hidden');
                sectionManual.classList.remove('hidden');
                // Limpiamos OC
                inputOc.value = "";
                inputOc.required = false;
                inputProd.required = true;
            }
        }

        // Validación especial para transacciones de tipo Cancelado (T07)
        function checkTransactionType() {
            const selectTrn = document.getElementById('TRN_Codigo');
            const selectedOption = selectTrn.options[selectTrn.selectedIndex];
            const tipo = selectedOption.getAttribute('data-tipo');

            // Si el código es T07 (Cancelado), forzamos a que sea por Orden de Compra
            if (selectTrn.value === 'T07') {
                alert('Las cancelaciones (T07) deben estar asociadas obligatoriamente a una Orden de Compra.');
                document.querySelector('input[value="oc"]').checked = true;
                toggleModo('oc');
            }
        }

        // Prevenir envío si falta información según el modo
        document.getElementById('kardex-form').addEventListener('submit', function(e) {
            const modo = document.querySelector('input[name="modo"]:checked').value;
            const trn = document.getElementById('TRN_Codigo').value;

            if (!trn) {
                e.preventDefault();
                alert('Por favor, seleccione un tipo de transacción.');
                return;
            }

            if (modo === 'oc' && !document.getElementById('ORC_Numero').value) {
                e.preventDefault();
                alert('Debe seleccionar una Orden de Compra.');
            } else if (modo === 'manual' && (!document.getElementById('PRO_Codigo').value || !document
                    .getElementById('KAR_cantidad').value)) {
                e.preventDefault();
                alert('Debe completar el producto y la cantidad para el ajuste manual.');
            }
        });
    </script>
@endsection
