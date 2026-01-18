@extends('components.admin-layout')

@section('page-title', 'Editar Bodega')

@section('content')
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white dark:bg-zinc-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-bold mb-6 dark:text-white">Editar Bodega: {{ $bodega->BOD_Codigo }}</h2>

            <form action="{{ route('warehouse.update', $bodega->BOD_Codigo) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de Bodega</label>
                        <input type="text" name="BOD_Nombre" value="{{ old('BOD_Nombre', $bodega->BOD_Nombre) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                        @error('BOD_Nombre')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Responsable -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Responsable</label>
                        <input type="text" name="BOD_Responsable"
                            value="{{ old('BOD_Responsable', $bodega->BOD_Responsable) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                        @error('BOD_Responsable')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección</label>
                        <input type="text" name="BOD_Direccion"
                            value="{{ old('BOD_Direccion', $bodega->BOD_Direccion) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                        @error('BOD_Direccion')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Ciudad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciudad</label>
                        <input type="text" name="BOD_Ciudad" value="{{ old('BOD_Ciudad', $bodega->BOD_Ciudad) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                        @error('BOD_Ciudad')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- País -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">País</label>
                        <input type="text" name="BOD_Pais" value="{{ old('BOD_Pais', $bodega->BOD_Pais) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                        @error('BOD_Pais')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Código Postal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código Postal</label>
                        <input type="text" name="BOD_CodigoPostal"
                            value="{{ old('BOD_CodigoPostal', $bodega->BOD_CodigoPostal) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-zinc-900 dark:text-white" required>
                        @error('BOD_CodigoPostal')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('warehouse.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 dark:bg-zinc-700 dark:text-white">Cancelar</a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
