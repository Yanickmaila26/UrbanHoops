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
                       class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white dark:hover:bg-zinc-600">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
                {{-- IMPORTANTE: enctype para permitir el envío de la imagen --}}
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Información del Producto
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="PRO_Codigo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Código de Producto *
                                    </label>
                                    <input type="text" name="PRO_Codigo" id="PRO_Codigo"
                                           value="{{ old('PRO_Codigo') }}" required maxlength="15"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm @error('PRO_Codigo') border-red-500 @enderror"
                                           placeholder="Ej: PROD-001">
                                    @error('PRO_Codigo')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="PRO_Nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nombre del Producto *
                                    </label>
                                    <input type="text" name="PRO_Nombre" id="PRO_Nombre" value="{{ old('PRO_Nombre') }}"
                                           required maxlength="60"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm @error('PRO_Nombre') border-red-500 @enderror"
                                           placeholder="Nombre comercial">
                                    @error('PRO_Nombre')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="PRO_Descripcion_Corta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Descripción Corta *
                                </label>
                                <input type="text" name="PRO_Descripcion_Corta" id="PRO_Descripcion_Corta"
                                       value="{{ old('PRO_Descripcion_Corta') }}" required maxlength="100"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm"
                                       placeholder="Breve resumen del producto">
                            </div>

                            <div>
                                <label for="PRO_Descripcion_Larga" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Descripción Detallada *
                                </label>
                                <textarea name="PRO_Descripcion_Larga" id="PRO_Descripcion_Larga" rows="4" required
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm"
                                          placeholder="Especificaciones técnicas, materiales, etc.">{{ old('PRO_Descripcion_Larga') }}</textarea>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Imagen del Producto
                            </h3>
                            <div class="flex items-center space-x-6">
                                <div class="shrink-0">
                                    <img id="preview" class="h-24 w-24 object-cover rounded-lg bg-gray-100 dark:bg-zinc-700"
                                         src="https://via.placeholder.com/150" alt="Vista previa">
                                </div>
                                <label class="block">
                                    <span class="sr-only">Elegir imagen</span>
                                    <input type="file" name="PRO_Imagen" id="PRO_Imagen" accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-700 dark:file:text-white">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG o JPEG (Máx. 2MB)</p>
                                </label>
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
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Guardar Producto
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
