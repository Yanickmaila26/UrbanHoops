@extends('components.admin-layout')

@section('page-title', 'Nueva Subcategoría')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-5">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Crear Nueva Subcategoría
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Ingresa los datos de la nueva subcategoría.
                        </p>
                    </div>

                    <form action="{{ route('subcategories.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Categoría -->
                            <div>
                                <label for="CAT_Codigo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Categoría Principal
                                </label>
                                <select id="CAT_Codigo" name="CAT_Codigo" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm">
                                    <option value="">Seleccione una categoría</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->CAT_Codigo }}"
                                            {{ old('CAT_Codigo') == $categoria->CAT_Codigo ? 'selected' : '' }}>
                                            {{ $categoria->CAT_Nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('CAT_Codigo')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nombre -->
                            <div>
                                <label for="SCT_Nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nombre de la Subcategoría
                                </label>
                                <input type="text" name="SCT_Nombre" id="SCT_Nombre"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm"
                                    value="{{ old('SCT_Nombre') }}" required>
                                @error('SCT_Nombre')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('subcategories.index') }}"
                                class="mr-3 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-zinc-600 transition">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Guardar Subcategoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
