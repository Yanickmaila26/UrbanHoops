@extends('components.admin-layout')

@section('page-title', 'Nueva Categoría')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-5">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Crear Nueva Categoría
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Ingresa el nombre de la nueva categoría.
                        </p>
                    </div>

                    <form action="{{ route('categories.store') }}" method="POST" x-data="{ loading: false }"
                        @submit="loading = true">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Nombre -->
                            <div>
                                <label for="CAT_Nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nombre de la Categoría
                                </label>
                                <input type="text" name="CAT_Nombre" id="CAT_Nombre"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white sm:text-sm"
                                    value="{{ old('CAT_Nombre') }}" required>
                                @error('CAT_Nombre')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('categories.index') }}"
                                class="mr-3 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-zinc-600 transition">
                                Cancelar
                            </a>
                            <button type="submit" :disabled="loading"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-show="!loading">Guardar Categoría</span>
                                <span x-show="loading">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
