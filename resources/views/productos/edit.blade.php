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
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Código Único (No
                                editable)</label>
                            <input type="text" name="PRO_Codigo" value="{{ $producto->PRO_Codigo }}" readonly
                                class="w-full rounded-md border-gray-300 bg-gray-100 dark:bg-zinc-700 cursor-not-allowed">
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
                            <label for="PRO_Stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock
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
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:ring-2 focus:ring-yellow-500 transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('PRO_Imagen').onchange = evt => {
            const [file] = document.getElementById('PRO_Imagen').files
            if (file) {
                document.getElementById('preview').src = URL.createObjectURL(file)
            }
        }
    </script>
@endsection
