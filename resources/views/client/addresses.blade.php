@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-brand">Mi Perfil y Direcciones</h1>

        <div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
            <form action="{{ route('client.addresses.update') }}" method="POST">
                @csrf
                @method('PUT')

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6">
                    <!-- Read-only fields -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Cédula / RUC</label>
                        <input type="text" value="{{ $client->CLI_Ced_Ruc }}" disabled
                            class="shadow-sm bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nombre</label>
                        <input type="text" value="{{ $client->CLI_Nombre }}" disabled
                            class="shadow-sm bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="text" value="{{ $client->CLI_Correo }}" disabled
                            class="shadow-sm bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>

                    <!-- Editable fields -->
                    <div>
                        <label for="telefono" class="block text-gray-700 text-sm font-bold mb-2">Teléfono</label>
                        <input type="text" name="telefono" id="telefono"
                            value="{{ old('telefono', $client->CLI_Telefono) }}"
                            class="shadow-sm bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
                            required>
                        @error('telefono')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="direccion" class="block text-gray-700 text-sm font-bold mb-2">Dirección de Envío
                            Principal</label>
                        <textarea name="direccion" id="direccion" rows="3"
                            class="shadow-sm bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
                            required>{{ old('direccion', $client->CLI_Direccion) }}</textarea>
                        <p class="text-gray-500 text-xs mt-1">Esta dirección se utilizará por defecto en tus nuevos pedidos.
                        </p>
                        @error('direccion')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-brand">Actualizar Información</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
