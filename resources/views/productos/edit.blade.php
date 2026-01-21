@extends('components.admin-layout')

@section('page-title', 'Editar Producto')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Editar Producto: {{ $producto->PRO_Nombre }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Modifique los datos técnicos o actualice la imagen del producto
                        </p>
                    </div>
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                    <p class="font-bold">Por favor corrija los siguientes errores:</p>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
                <form action="{{ route('products.update', $producto->PRO_Codigo) }}" method="POST"
                    enctype="multipart/form-data" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Código Único (No
                                    editable)</label>
                                <input type="text" name="PRO_Codigo" value="{{ $producto->PRO_Codigo }}" readonly
                                    class="w-full rounded-md border-gray-300 bg-gray-100 dark:bg-zinc-700 cursor-not-allowed">
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
                                            {{ old('PRV_Ced_Ruc', $producto->PRV_Ced_Ruc) == $proveedor->PRV_Ced_Ruc ? 'selected' : '' }}>
                                            {{ $proveedor->PRV_Nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('PRV_Ced_Ruc')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="SCT_Codigo"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subcategoría</label>
                            <select name="SCT_Codigo" id="SCT_Codigo"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <option value="">Seleccione Subcategoría</option>
                                @foreach ($subcategorias as $sub)
                                    <option value="{{ $sub->SCT_Codigo }}"
                                        {{ old('SCT_Codigo', $producto->SCT_Codigo) == $sub->SCT_Codigo ? 'selected' : '' }}>
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
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre
                                del
                                Producto</label>
                            <input type="text" name="PRO_Nombre" id="PRO_Nombre"
                                value="{{ old('PRO_Nombre', $producto->PRO_Nombre) }}"
                                placeholder="Ej: Zapatillas Urban Pro"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>
                            @error('PRO_Nombre')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="PRO_Descripcion"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                            <textarea name="PRO_Descripcion" id="PRO_Descripcion" rows="3"
                                placeholder="Escriba una descripción detallada sin caracteres especiales..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>{{ old('PRO_Descripcion', $producto->PRO_Descripcion) }}</textarea>
                            @error('PRO_Descripcion')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="PRO_Marca"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Marca</label>
                            <input type="text" name="PRO_Marca" id="PRO_Marca"
                                value="{{ old('PRO_Marca', $producto->PRO_Marca) }}"
                                placeholder="Ej: Nike, Adidas, Genérico"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>
                            @error('PRO_Marca')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="PRO_Color"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color</label>
                                <input type="text" name="PRO_Color" id="PRO_Color"
                                    value="{{ old('PRO_Color', $producto->PRO_Color) }}" placeholder="Ej: Negro/Rojo"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                    required>
                                @error('PRO_Color')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="PRO_Talla"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Talla</label>
                                <input type="text" name="PRO_Talla" id="PRO_Talla"
                                    value="{{ old('PRO_Talla', $producto->PRO_Talla) }}" placeholder="Ej: XL, 40, L"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                    required>
                                @error('PRO_Talla')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="PRO_Precio"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio
                                ($)</label>
                            <input type="number" step="0.01" min="0" name="PRO_Precio" id="PRO_Precio"
                                value="{{ old('PRO_Precio', $producto->PRO_Precio) }}" placeholder="0.00" min="0"
                                step="0.1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>
                            @error('PRO_Precio')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="PRO_Stock"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock
                                Actual</label>
                            <input type="number" min="0" name="PRO_Stock" id="PRO_Stock"
                                value="{{ old('PRO_Stock', $producto->PRO_Stock) }}" placeholder="Cantidad en almacén"
                                min="0" step="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white"
                                required>
                            @error('PRO_Stock')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Imagen del
                                Producto</label>
                            <div class="mt-2 flex items-center space-x-6">
                                <div class="shrink-0">
                                    <img id="preview"
                                        class="h-32 w-32 object-cover rounded-lg border dark:border-zinc-700 bg-gray-50"
                                        src="{{ asset($producto->PRO_Imagen) }}" alt="Vista previa">
                                </div>
                                <label class="block">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Subir nueva imagen (dejar vacío
                                        para mantener la actual)</span>
                                    <input type="file" name="PRO_Imagen" id="PRO_Imagen" accept="image/*"
                                        onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0])"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-700 dark:file:text-white">
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
                        <button type="submit" <button type="submit" :disabled="loading"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span x-show="!loading">Actualizar Producto</span>
                            <span x-show="loading">Actualizando...</span>
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
