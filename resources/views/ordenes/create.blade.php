@extends('components.admin-layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('purchase-orders.store') }}" method="POST" id="order-form" x-data="ordenForm()"
            x-init="initForm()" @submit="loading = true">
            @csrf

            <!-- Error Message -->
            <template x-if="errorMessage">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline" x-text="errorMessage"></span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="errorMessage = ''">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <title>Close</title>
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </span>
                </div>
            </template>

            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 dark:text-white">Nueva Orden de Compra</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Número de Orden</label>
                        <input type="text" name="ORC_Numero" value="{{ $nuevoCodigo }}" readonly
                            class="bg-gray-100 w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Proveedor</label>
                        <select name="PRV_Ced_Ruc" id="proveedor-select"
                            class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                            <option value="">Seleccione Proveedor</option>
                            @foreach ($proveedores as $prov)
                                <option value="{{ $prov->PRV_Ced_Ruc }}">{{ $prov->PRV_Nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Fecha Emisión</label>
                        <input type="date" name="ORC_Fecha_Emision" value="{{ date('Y-m-d') }}" x-model="fechaEmision"
                            class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Fecha Entrega</label>
                        <input type="date" name="ORC_Fecha_Entrega" x-model="fechaEntrega" @change="validateDates()"
                            class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold dark:text-white">Productos de la Orden</h3>
                    <button type="button" @click="addRow()"
                        class="bg-green-600 text-white px-3 py-1 rounded-md text-sm hover:bg-green-700">+ Agregar
                        Producto</button>
                </div>

                <table class="w-full" id="items-table">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b">
                            <th class="pb-2">Producto</th>
                            <th class="pb-2 w-32">Talla</th>
                            <th class="pb-2 w-32">Cantidad</th>
                            <th class="pb-2 w-32">Precio Unit.</th>
                            <th class="pb-2 w-32">Subtotal</th>
                            <th class="pb-2 w-16"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <template x-for="(row, index) in rows" :key="row.id">
                            <tr class="border-b dark:border-zinc-700">
                                <td class="py-3">
                                    <select name="productos[]"
                                        class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white select-product"
                                        x-init="initSelect2($el, row)" required>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </td>
                                <td class="py-3">
                                    <select name="tallas[]" x-model="row.talla" required
                                        class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white"
                                        @change="checkDuplicate(row)">
                                        <option value="">...</option>
                                        <template x-for="size in row.availableSizes" :key="size.talla">
                                            <option :value="size.talla" x-text="size.talla"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="py-3">
                                    <input type="number" name="cantidades[]" min="1" x-model="row.qty"
                                        class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                                </td>
                                <td class="py-3">
                                    <input type="number" step="0.01" name="precios[]" :value="row.price"
                                        class="w-full border-none bg-transparent dark:text-gray-300" readonly>
                                </td>
                                <td class="py-3">
                                    <input type="number" step="0.01"
                                        class="w-full border-none bg-transparent font-semibold dark:text-white"
                                        :value="(row.qty * row.price).toFixed(2)" readonly>
                                </td>
                                <td class="py-3 text-center">
                                    <button type="button" @click="removeRow(row.id)"
                                        class="text-red-500 hover:text-red-700">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-bold py-4 dark:text-white">Monto Total:</td>
                            <td class="py-4">
                                <input type="number" step="0.01" name="ORC_Monto_Total" id="total-final" readonly
                                    class="w-full font-bold border-none bg-transparent dark:text-blue-400"
                                    :value="totalOrden">
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mt-6 flex justify-end">
                    <button type="submit" :disabled="loading || rows.length === 0"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition">
                        <span x-show="!loading">Guardar Orden</span>
                        <span x-show="loading">Guardando...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('ordenForm', () => ({
                rows: [],
                loading: false,
                errorMessage: '',
                productos: @json($productos),
                fechaEmision: '{{ date('Y-m-d') }}',
                fechaEntrega: '',

                get totalOrden() {
                    return this.rows.reduce((sum, row) => sum + (row.qty * row.price), 0).toFixed(
                        2);
                },

                initForm() {
                    // Init Proveedor Select2
                    $('#proveedor-select').select2({
                        placeholder: "Seleccione Proveedor",
                        width: '100%',
                        allowClear: true
                    });

                    this.addRow();
                },

                addRow() {
                    this.rows.push({
                        id: Date.now(),
                        qty: 1,
                        price: 0,
                        code: '',
                        talla: '',
                        availableSizes: []
                    });
                },

                removeRow(id) {
                    this.rows = this.rows.filter(r => r.id !== id);
                },

                validateDates() {
                    if (this.fechaEntrega && this.fechaEntrega < this.fechaEmision) {
                        this.showError("La fecha de entrega no puede ser menor a la de emisión");
                        this.fechaEntrega = this.fechaEmision;
                    }
                },

                showError(msg) {
                    this.errorMessage = msg;
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    setTimeout(() => this.errorMessage = '', 5000);
                },

                initSelect2(el, row) {
                    const options = this.productos.map(p =>
                        `<option value="${p.PRO_Codigo}">${p.PRO_Nombre} - ${p.PRO_Codigo}</option>`
                    ).join('');

                    $(el).html('<option value="">Seleccione...</option>' + options);

                    $(el).select2({
                        placeholder: "Buscar Producto",
                        width: '100%'
                    });

                    $(el).on('select2:select', (e) => {
                        const selectedId = e.params.data.id;
                        const selected = this.productos.find(p => p.PRO_Codigo == selectedId);

                        if (selected) {
                            row.price = selected.PRO_Precio;
                            row.code = selected.PRO_Codigo;

                            let sizes = selected.PRO_Talla;
                            if (typeof sizes === 'string') {
                                try {
                                    sizes = JSON.parse(sizes);
                                } catch (e) {
                                    console.error('Error parsing sizes for PO:', e);
                                    sizes = [];
                                }
                            }
                            // Double decoding check if needed
                            if (typeof sizes === 'string') {
                                try {
                                    sizes = JSON.parse(sizes);
                                } catch (e) {
                                    sizes = [];
                                }
                            }

                            row.availableSizes = sizes || [];
                            row.talla = ''; // Reset talla
                        }
                    });
                },

                checkDuplicate(row) {
                    if (!row.code || !row.talla) return;

                    const isDuplicate = this.rows.some(r => r.id !== row.id && r.code === row.code && r
                        .talla === row.talla);

                    if (isDuplicate) {
                        this.showError("Este producto y talla ya han sido agregados.");
                        row.talla = ''; // Reset duplicate
                    }
                }
            }));
        });
    </script>
@endpush
