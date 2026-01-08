@extends('components.admin-layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('purchase-orders.update', $orden->ORC_Numero) }}" method="POST" id="order-form">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 dark:text-white">Editar Orden #{{ $orden->ORC_Numero }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Proveedor</label>
                        <select name="PRV_Ced_Ruc" class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white"
                            required>
                            @foreach ($proveedores as $prov)
                                <option value="{{ $prov->PRV_Ced_Ruc }}"
                                    {{ $orden->PRV_Ced_Ruc == $prov->PRV_Ced_Ruc ? 'selected' : '' }}>
                                    {{ $prov->PRV_Nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Fecha Emisión</label>
                        <input type="date" name="ORC_Fecha_Emision" value="{{ $orden->ORC_Fecha_Emision }}"
                            class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Fecha Entrega</label>
                        <input type="date" name="ORC_Fecha_Entrega" value="{{ $orden->ORC_Fecha_Entrega }}"
                            class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold dark:text-white">Productos de la Orden</h3>
                    <button type="button" onclick="addRow()" class="bg-green-600 text-white px-3 py-1 rounded-md text-sm">+
                        Añadir</button>
                </div>

                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm border-b dark:border-zinc-700">
                            <th class="pb-2">Producto</th>
                            <th class="pb-2 w-32">Cantidad</th>
                            <th class="pb-2 w-32">Precio Unit.</th>
                            <th class="pb-2 w-32">Subtotal</th>
                            <th class="pb-2 w-16"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        @foreach ($orden->productos as $item)
                            <tr id="row-{{ $loop->index }}" class="border-b dark:border-zinc-700">
                                <td class="py-3">
                                    <select name="productos[]" onchange="validateSelection(this, {{ $loop->index }})"
                                        class="w-full rounded-md dark:bg-zinc-900 dark:text-white select-product" required>
                                        @foreach ($productos as $p)
                                            <option value="{{ $p->PRO_Codigo }}" data-price="{{ $p->PRO_Precio }}"
                                                {{ $item->PRO_Codigo == $p->PRO_Codigo ? 'selected' : '' }}>
                                                {{ $p->PRO_Nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-3">
                                    <input type="number" name="cantidades[]"
                                        value="{{ $item->pivot->cantidad_solicitada }}" min="1"
                                        oninput="calculateSubtotal({{ $loop->index }})"
                                        class="w-full rounded-md dark:bg-zinc-900 dark:text-white">
                                </td>
                                <td class="py-3">
                                    <input type="number" name="precios[]" value="{{ $item->PRO_Precio }}"
                                        class="w-full border-none bg-transparent dark:text-gray-300" readonly>
                                </td>
                                <td class="py-3">
                                    <input type="number" step="0.01"
                                        class="subtotal w-full border-none bg-transparent font-semibold dark:text-white"
                                        value="{{ $item->pivot->cantidad_solicitada * $item->PRO_Precio }}" readonly>
                                </td>
                                <td class="py-3 text-center">
                                    <button type="button" onclick="removeRow({{ $loop->index }})"
                                        class="text-red-500">✕</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-bold py-4 dark:text-white">TOTAL:</td>
                            <td><input type="number" step="0.01" name="ORC_Monto_Total" id="total-final"
                                    value="{{ $orden->ORC_Monto_Total }}" readonly
                                    class="w-full font-bold border-none bg-transparent dark:text-blue-400"></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mt-6 flex justify-end gap-2">
                    <a href="{{ route('purchase-orders.index') }}" class="px-6 py-2 bg-gray-200 rounded-md">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md">Actualizar Orden</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const productosDisp = @json($productos);

        function addRow() {
            const tbody = document.getElementById('items-body');
            const rowId = Date.now();
            const html = `
            <tr id="row-${rowId}" class="border-b dark:border-zinc-700">
                <td class="py-3">
                    <select name="productos[]" 
                            onchange="validateSelection(this, ${rowId})" 
                            class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white select-product" 
                            required>
                        <option value="">Seleccione...</option>
                        ${productosDisp.map(p => `<option value="${p.PRO_Codigo}" data-price="${p.PRO_Precio}">${p.PRO_Nombre}</option>`).join('')}
                    </select>
                </td>
                <td class="py-3">
                    <input type="number" name="cantidades[]" min="1" value="1" oninput="calculateSubtotal(${rowId})" class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                </td>
                <td class="py-3">
                    <input type="number" step="0.01" name="precios[]" class="w-full border-none bg-transparent dark:text-gray-300" readonly>
                </td>
                <td class="py-3">
                    <input type="number" step="0.01" class="subtotal w-full border-none bg-transparent font-semibold dark:text-white" readonly value="0.00">
                </td>
                <td class="py-3 text-center">
                    <button type="button" onclick="removeRow(${rowId})" class="text-red-500 hover:text-red-700">✕</button>
                </td>
            </tr>
        `;
            tbody.insertAdjacentHTML('beforeend', html);
        }

        // Nueva función para validar duplicados y actualizar precio
        function validateSelection(select, rowId) {
            const selectedValue = select.value;
            const allSelects = document.querySelectorAll('.select-product');
            let duplicate = false;

            allSelects.forEach(s => {
                // Comparamos si el valor existe en otro select que no sea el actual
                if (s !== select && s.value === selectedValue && selectedValue !== "") {
                    duplicate = true;
                }
            });

            if (duplicate) {
                alert("Este producto ya ha sido agregado a la orden. Por favor, ajuste la cantidad en la fila existente.");
                select.value = ""; // Resetear el select
                updatePrice(select, rowId); // Limpiar precio y subtotal
                return;
            }

            updatePrice(select, rowId);
        }

        function updatePrice(select, rowId) {
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption ? selectedOption.getAttribute('data-price') : 0;
            const row = document.getElementById(`row-${rowId}`);
            row.querySelector('input[name="precios[]"]').value = price || 0;
            calculateSubtotal(rowId);
        }

        function calculateSubtotal(rowId) {
            const row = document.getElementById(`row-${rowId}`);
            if (!row) return;

            const qty = row.querySelector('input[name="cantidades[]"]').value || 0;
            const price = row.querySelector('input[name="precios[]"]').value || 0;
            const subtotalInput = row.querySelector('.subtotal');

            const subtotal = (qty * price).toFixed(2);
            subtotalInput.value = subtotal;

            calculateTotalFinal();
        }

        function calculateTotalFinal() {
            let total = 0;
            document.querySelectorAll('.subtotal').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('total-final').value = total.toFixed(2);
        }

        function removeRow(rowId) {
            document.getElementById(`row-${rowId}`).remove();
            calculateTotalFinal();
        }

        document.addEventListener('DOMContentLoaded', addRow);
    </script>
@endsection
