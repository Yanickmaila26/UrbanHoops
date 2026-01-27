@extends('components.admin-layout')

@section('page-title', 'Crear Producto')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Crear Nuevo Producto
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Complete los detalles técnicos y visuales del producto
                        </p>
                    </div>
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver al listado
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
                    x-data="{ loading: false }" @submit="loading = true">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 dark:bg-red-900/30 dark:border-red-600">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                        Hay errores en el formulario:
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="PRO_Codigo"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código de Producto
                                (Automático)</label>
                            <input type="text" name="PRO_Codigo" id="PRO_Codigo" value="{{ $nuevoCodigo }}" readonly
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm font-mono font-bold text-blue-600 dark:bg-zinc-900 dark:border-zinc-700 cursor-not-allowed"
                                placeholder="P000">
                            <p class="text-xs text-gray-500 mt-1">Automático</p>
                        </div>

                        <div>
                            <label for="PRV_Ced_Ruc"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Proveedor</label>
                            <select name="PRV_Ced_Ruc" id="PRV_Ced_Ruc"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>
                                <option value="">Seleccione Proveedor</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->PRV_Ced_Ruc }}"
                                        {{ old('PRV_Ced_Ruc') == $proveedor->PRV_Ced_Ruc ? 'selected' : '' }}>
                                        {{ $proveedor->PRV_Nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('PRV_Ced_Ruc')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="SCT_Codigo"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subcategoría</label>
                            <select name="SCT_Codigo" id="SCT_Codigo"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <option value="">Seleccione Subcategoría</option>
                                @foreach ($subcategorias as $sub)
                                    <option value="{{ $sub->SCT_Codigo }}"
                                        {{ old('SCT_Codigo') == $sub->SCT_Codigo ? 'selected' : '' }}>
                                        {{ $sub->categoria->CAT_Nombre }} - {{ $sub->SCT_Nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('SCT_Codigo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="PRO_Nombre"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del
                                Producto</label>
                            <input type="text" name="PRO_Nombre" id="PRO_Nombre" value="{{ old('PRO_Nombre') }}"
                                placeholder="Ej: Zapatillas Urban Pro"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>
                            @error('PRO_Nombre')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="PRO_Descripcion"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción
                                General</label>
                            <textarea name="PRO_Descripcion" id="PRO_Descripcion" rows="3"
                                placeholder="Escriba una descripción detallada sin caracteres especiales..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>{{ old('PRO_Descripcion') }}</textarea>
                            @error('PRO_Descripcion')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="PRO_Marca"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Marca</label>
                            <input type="text" name="PRO_Marca" id="PRO_Marca" value="{{ old('PRO_Marca') }}"
                                placeholder="Ej: Nike, Adidas, Genérico"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>
                            @error('PRO_Marca')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="PRO_Color"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color</label>
                            <input type="text" name="PRO_Color" id="PRO_Color" value="{{ old('PRO_Color') }}"
                                placeholder="Ej: Rojo, Azul/Blanco"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>
                            @error('PRO_Color')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2" x-data="{
                            sizes: {{ old('PRO_Talla') ? json_encode(array_values(old('PRO_Talla'))) : '[{ "talla": "", "stock": 0 }]' }},
                            get totalStock() {
                                return this.sizes.reduce((sum, item) => sum + (parseInt(item.stock) || 0), 0);
                            },
                            addSize() {
                                this.sizes.push({ talla: '', stock: 0 });
                            },
                            removeSize(index) {
                                if (this.sizes.length > 1) {
                                    this.sizes.splice(index, 1);
                                }
                            }
                        }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tallas y Stock
                            </label>

                            <div class="space-y-3">
                                <template x-for="(size, index) in sizes" :key="index">
                                    <div class="flex gap-4 items-start">
                                        <div class="flex-1">
                                            <input type="text" :name="`PRO_Talla[${index}][talla]`"
                                                x-model="size.talla" placeholder="Talla (ej: S, 42)" required
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white sm:text-sm">
                                        </div>
                                        <div class="w-32">
                                            <input type="number" :name="`PRO_Talla[${index}][stock]`"
                                                x-model="size.stock" min="0" placeholder="Cant." required
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white sm:text-sm">
                                        </div>
                                        <button type="button" @click="removeSize(index)"
                                            class="mt-1 text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <button type="button" @click="addSize()"
                                class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Agregar otra talla
                            </button>

                            <div class="mt-4 p-3 bg-gray-50 dark:bg-zinc-700 rounded-md">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Stock Total Calculado: <span x-text="totalStock"
                                        class="font-bold text-blue-600 dark:text-blue-400"></span>
                                </p>
                                <input type="hidden" name="PRO_Stock" :value="totalStock">
                            </div>

                            @if ($errors->has('PRO_Talla.*'))
                                <div class="mt-2 text-sm text-red-600">
                                    <ul class="list-disc pl-5">
                                        @foreach ($errors->get('PRO_Talla.*') as $field => $messages)
                                            @foreach ($messages as $message)
                                                <li>{{ $message }}</li>
                                            @endforeach
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Asignar a Bodegas
                            </label>
                            <p class="text-xs text-gray-500 mb-2">Selecciona las bodegas donde estará disponible este
                                producto (stock inicial: 0)</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach ($bodegas as $bodega)
                                    <label
                                        class="flex items-center p-3 border border-gray-300 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer transition">
                                        <input type="checkbox" name="bodegas[]" value="{{ $bodega->BOD_Codigo }}"
                                            {{ is_array(old('bodegas')) && in_array($bodega->BOD_Codigo, old('bodegas')) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                        <span class="text-sm dark:text-white">{{ $bodega->BOD_Nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2 italic">Si no seleccionas ninguna bodega, el producto no
                                estará disponible en inventario.</p>
                        </div>

                        <div>
                            <label for="PRO_Precio"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio ($)</label>
                            <input type="number" step="0.01" min="0" name="PRO_Precio" id="PRO_Precio"
                                value="{{ old('PRO_Precio') }}" x-on:input="$el.value < 0 ? $el.value = 0 : null"
                                placeholder="0.00"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>
                            @error('PRO_Precio')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock field removed as it is now calculated dynamically -->
                        <div class="hidden">
                            <!-- PRO_Stock is hidden input above -->
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Imagen del
                                Producto</label>
                            <div class="mt-2 flex items-center space-x-6">
                                <div class="shrink-0">
                                    <img id="preview"
                                        class="h-32 w-32 object-cover rounded-lg bg-gray-100 dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700"
                                        src="https://via.placeholder.com/150?text=Sin+Imagen" alt="Vista previa">
                                </div>
                                <label class="block">
                                    <span class="sr-only">Elegir imagen</span>
                                    <input type="file" name="PRO_Imagen" id="PRO_Imagen" accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-700 dark:file:text-white"
                                        onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0])">
                                </label>
                            </div>
                            @error('PRO_Imagen')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3 border-t pt-6 dark:border-zinc-700">
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition dark:bg-zinc-700 dark:text-white">
                            Cancelar
                        </a>
                        <button type="submit" :disabled="loading"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span x-show="!loading">Guardar Producto</span>
                            <span x-show="loading">Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- Initialize Select2 -->
    <script>
        $(document).ready(function() {
            $('#PRV_Ced_Ruc').select2({
                placeholder: "Seleccione Proveedor",
                allowClear: true,
                width: '100%'
            });
            $('#SCT_Codigo').select2({
                placeholder: "Seleccione Subcategoría",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush
