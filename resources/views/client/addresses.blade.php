@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8" x-data="{ deleteModal: false, selectedAddress: {} }">
        <h1 class="text-3xl font-bold mb-6 text-brand">Mis Datos de Facturación</h1>

        <!-- List of Saved Profiles -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Perfiles Guardados</h2>
            @if ($profiles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($profiles as $profile)
                        <div class="bg-white rounded-lg shadow p-4 border relative">
                            <form action="{{ route('client.billing.destroy', $profile->DAF_Codigo) }}" method="POST"
                                class="absolute top-2 right-2" onsubmit="return confirm('¿Eliminar este perfil?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                            <p class="font-bold text-gray-800">{{ $profile->DAF_Ciudad }}, {{ $profile->DAF_Estado }}</p>
                            <p class="text-sm text-gray-600">{{ Str::limit($profile->DAF_Direccion, 50) }}</p>
                            <p class="text-sm text-gray-500 mt-2">CP: {{ $profile->DAF_CP }}</p>
                            <p class="text-xs text-gray-400 mt-1">Tarjeta: ****
                                {{ substr($profile->DAF_Tarjeta_Numero, -4) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic">No tienes perfiles guardados.</p>
            @endif
        </div>

        <!-- Add New Profile Form -->
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Agregar Nuevo Perfil</h2>
            <form action="{{ route('client.billing.store') }}" method="POST">
                @csrf

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Client Info -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Teléfono Contacto</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $client->CLI_Telefono) }}"
                            class="shadow-sm bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5"
                            required>
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion') }}"
                            class="shadow-sm bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5"
                            required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Ciudad</label>
                        <input type="text" name="ciudad" value="{{ old('ciudad') }}"
                            class="shadow-sm bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5"
                            required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Estado / Provincia</label>
                        <input type="text" name="estado" value="{{ old('estado') }}"
                            class="shadow-sm bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5"
                            required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Código Postal</label>
                        <input type="text" name="cp" value="{{ old('cp') }}"
                            class="shadow-sm bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5"
                            required>
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                <!-- Payment Info -->
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Datos de Tarjeta (para futuros pagos)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-3">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Número de Tarjeta</label>
                        <input type="text" name="card_number" id="card_number" placeholder="0000 0000 0000 0000"
                            class="shadow-sm bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5"
                            required maxlength="19">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Expiración (MM/YY)</label>
                        <input type="text" name="card_expiry" id="card_expiry" placeholder="MM/YY"
                            class="shadow-sm bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5"
                            required maxlength="5">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">CVV</label>
                        <input type="password" name="card_cvv" placeholder="123"
                            class="shadow-sm bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5"
                            required maxlength="4">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="btn btn-brand px-6 py-2 bg-black text-white rounded hover:bg-gray-800">Guardar
                        Perfil</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('card_number').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
        });
        document.getElementById('card_expiry').addEventListener('input', function(e) {
            let input = e.target.value.replace(/\D/g, '');
            if (input.length > 2) input = input.substring(0, 2) + '/' + input.substring(2);
            e.target.value = input;
        });
    </script>
@endsection
