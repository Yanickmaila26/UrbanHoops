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
                            Modifique los campos necesarios del producto
                        </p>
                    </div>
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
                <form action="{{ route('products.update', $producto->PRO_Codigo) }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Información General
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="PRO_Codigo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Código de Producto *
                                    </label>
                                    <input type="text" name="PRO_Codigo" id="PRO_Codigo"
                                           value="{{ old('PRO_Codigo', $producto->PRO_Codigo) }}" readonly
                                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm dark:bg-zinc-600 dark:border-zinc-500 dark:text-gray-300 sm:text-sm">
                                    <p class="mt-1 text-xs text-gray-500">El código no es editable.</p>
                                </div>

                                <div>
                                    <label for="PRO_Nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nombre del Producto *
                                    </label>
                                    <input type="text" name="PRO_Nombre" id="PRO_Nombre"
                                           value="{{ old('PRO_Nombre', $producto->PRO_Nombre) }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="PRO_Descripcion_Corta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Descripción Corta *
                                </label>
                                <input type="text" name="PRO_Descripcion_Corta" id="PRO_Descripcion_Corta"
                                       value="{{ old('PRO_Descripcion_Corta', $producto->PRO_Descripcion_Corta) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm">
                            </div>

                            <div>
                                <label for="PRO_Descripcion_Larga" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Descripción Detallada *
                                </label>
                                <textarea name="PRO_Descripcion_Larga" id="PRO_Descripcion_Larga" rows="4" required
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm">{{ old('PRO_Descripcion_Larga', $producto->PRO_Descripcion_Larga) }}</textarea>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Imagen del Producto
                            </h3>
                            <div class="flex items-start space-x-6">
                                <div class="shrink-0 text-center">
                                    <p class="text-xs text-gray-500 mb-2">Vista previa / Actual</p>
                                    <img id="preview" class="h-32 w-32 object-cover rounded-lg border dark:border-zinc-600 bg-gray-100 dark:bg-zinc-700"
                                         src="{{ $producto->PRO_Imagen ? asset('storage/' . $producto->PRO_Imagen) : 'https://via.placeholder.com/150' }}"
                                         alt="Imagen actual">
                                </div>
                                <div class="flex-1">
                                    <label class="block">
                                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subir nueva imagen</span>
                                        <input type="file" name="PRO_Imagen" id="PRO_Imagen" accept="image/*"
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-700 dark:file:text-white">
                                    </label>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Deje este campo vacío si no desea cambiar la imagen actual.</p>
                                </div>
                            </div>
                            @error('PRO_Imagen')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition dark:bg-zinc-700 dark:text-white">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Script para previsualizar la nueva imagen seleccionada
        document.getElementById('PRO_Imagen').onchange = evt => {
            const [file] = document.getElementById('PRO_Imagen').files
            if (file) {
                document.getElementById('preview').src = URL.createObjectURL(file)
            }
        }
    </script>
@endsection
