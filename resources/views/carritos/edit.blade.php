@extends('components.admin-layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <form action="{{ route('shopping-carts.update', $carrito->CRC_Carrito) }}" method="POST" id="carrito-form"
            x-data="carritoFormEdit()" x-init="initForm({{ $carrito->productos->map(function ($p) {return ['id' => uniqid(), 'code' => $p->PRO_Codigo, 'qty' => $p->pivot->CRD_Cantidad, 'price' => $p->PRO_Precio];}) }})" @submit="loading = true">
            @csrf
            @method('PUT')

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
                <h2 class="text-xl font-bold mb-4 dark:text-white">Editar Carrito: {{ $carrito->CRC_Carrito }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Cliente</label>
                        <select id="cliente-select"
                            class="w-full rounded-md border-gray-300 bg-gray-100 dark:bg-zinc-800 dark:text-gray-400"
                            disabled>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->CLI_Ced_Ruc }}"
                                    {{ $carrito->CLI_Ced_Ruc == $cliente->CLI_Ced_Ruc ? 'selected' : '' }}>
                                    {{ $cliente->CLI_Ced_Ruc }} - {{ $cliente->CLI_Nombre }} {{ $cliente->CLI_Apellido }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="CLI_Ced_Ruc" value="{{ $carrito->CLI_Ced_Ruc }}">
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold dark:text-white">Productos</h3>
                    <button type="button" @click="addRow()"
                        class="bg-green-600 text-white px-3 py-1 rounded-md text-sm hover:bg-green-700">+ Agregar
                        Producto</button>
                </div>

                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b">
                            <th class="pb-2">Producto</th>
                            <th class="pb-2 w-32">Cantidad</th>
                            <th class="pb-2 w-32">Precio Unit.</th>
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
                                        <!-- Options injected via JS -->
                                    </select>
                                </td>
                                <td class="py-3">
                                    <input type="number" name="cantidades[]" min="1" x-model="row.qty"
                                        class="w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                                </td>
                                <td class="py-3">
                                    <input type="text" name="precios[]" :value="row.price"
                                        class="w-full border-none bg-transparent dark:text-gray-300" readonly>
                                </td>
                                <td class="py-3 text-center">
                                    <button type="button" @click="removeRow(row.id)"
                                        class="text-red-500 hover:text-red-700">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="mt-6 flex justify-end">
                    <button type="submit" :disabled="loading || rows.length === 0"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition disabled:opacity-50">
                        <span x-show="!loading">Actualizar Carrito</span>
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
            Alpine.data('carritoFormEdit', () => ({
                rows: [],
                loading: false,
                errorMessage: '',
                productos: @json($productos),

                initForm(initialRows) {
                    // Initialize Client Select2
                    $('#cliente-select').select2({
                        placeholder: "Seleccione Cliente",
                        width: '100%',
                        allowClear: true
                    });

                    // Load existing rows
                    this.rows = initialRows;
                },

                addRow() {
                    this.rows.push({
                        id: Date.now(),
                        qty: 1,
                        price: 0,
                        code: ''
                    });
                },

                removeRow(id) {
                    this.rows = this.rows.filter(r => r.id !== id);
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
                        `<option value="${p.PRO_Codigo}" data-price="${p.PRO_Precio}">${p.PRO_Nombre} - ${p.PRO_Codigo}</option>`
                    ).join('');

                    $(el).html('<option value="">Seleccione...</option>' + options);

                    $(el).select2({
                        placeholder: "Buscar Producto",
                        width: '100%'
                    });

                    // Set initial value if exists
                    if (row.code) {
                        $(el).val(row.code).trigger('change');
                    }

                    $(el).on('select2:select', (e) => {
                        const selectedId = e.params.data.id;

                        // Duplicate Check
                        const isDuplicate = this.rows.some(r => r.id !== row.id && r.code ===
                            selectedId);

                        if (isDuplicate) {
                            this.showError(
                                "Este producto ya está en el carrito. Ajuste la cantidad en su lugar."
                            );
                            $(el).val(null).trigger('change');
                            return;
                        }

                        const selected = this.productos.find(p => p.PRO_Codigo == selectedId);
                        if (selected) {
                            row.price = selected.PRO_Precio;
                            row.code = selected.PRO_Codigo;
                        }
                    });
                }
            }));
        });
    </script>
@endpush
