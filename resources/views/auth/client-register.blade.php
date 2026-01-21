<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Clientes - UrbanHoops</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <!-- component -->
    <div class="bg-sky-100 flex justify-center items-center min-h-screen py-10">
        <!-- Left: Image -->
        <div class="w-1/2 h-screen hidden lg:block fixed left-0 top-0">
            <img src="https://img.freepik.com/fotos-premium/imagen-fondo_910766-187.jpg?w=826" alt="UrbanHoops Background"
                class="object-cover w-full h-full">
        </div>
        <!-- Right: Register Form -->
        <div
            class="lg:w-1/2 ml-auto w-full px-4 lg:px-16 flex flex-col justify-center min-h-screen bg-sky-100 lg:bg-transparent">
            <div class="bg-white/80 lg:bg-transparent p-8 rounded-xl shadow-lg lg:shadow-none w-full max-w-lg mx-auto">
                <h1 class="text-2xl font-semibold mb-6 text-center lg:text-left">Registro de Clientes</h1>
                <form action="{{ route('client.register.submit') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Cedula Input -->
                        <div>
                            <label for="cli_ced_ruc" class="block text-gray-600 text-sm font-bold mb-1">Cédula /
                                RUC</label>
                            <input type="text" id="cli_ced_ruc" name="cli_ced_ruc" value="{{ old('cli_ced_ruc') }}"
                                class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500"
                                required placeholder="1234567890">
                            @error('cli_ced_ruc')
                                <span class="text-red-500 text-sm block">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Telefono Input -->
                        <div>
                            <label for="cli_telefono"
                                class="block text-gray-600 text-sm font-bold mb-1">Teléfono</label>
                            <input type="tel" id="cli_telefono" name="cli_telefono"
                                value="{{ old('cli_telefono') }}"
                                class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500"
                                required placeholder="0991234567">
                            @error('cli_telefono')
                                <span class="text-red-500 text-sm block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Nombre Input -->
                    <div class="mb-4">
                        <label for="cli_nombre" class="block text-gray-600 text-sm font-bold mb-1">Nombre
                            Completo</label>
                        <input type="text" id="cli_nombre" name="cli_nombre" value="{{ old('cli_nombre') }}"
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500"
                            required placeholder="Tu nombre y apellido">
                        @error('cli_nombre')
                            <span class="text-red-500 text-sm block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Direccion Input -->
                    <div class="mb-4">
                        <label for="cli_direccion" class="block text-gray-600 text-sm font-bold mb-1">Dirección</label>
                        <input type="text" id="cli_direccion" name="cli_direccion" value="{{ old('cli_direccion') }}"
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500"
                            required placeholder="Calle Principal y Secundaria">
                        @error('cli_direccion')
                            <span class="text-red-500 text-sm block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="mb-4">
                        <label for="email" class="block text-gray-600 text-sm font-bold mb-1">Correo
                            Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500"
                            autocomplete="email" required placeholder="tu@email.com">
                        @error('email')
                            <span class="text-red-500 text-sm block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-gray-800 text-sm font-bold mb-1">Contraseña</label>
                            <input type="password" id="password" name="password"
                                class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500"
                                autocomplete="new-password" required minlength="8">
                            @error('password')
                                <span class="text-red-500 text-sm block">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Confirm Password Input -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-gray-800 text-sm font-bold mb-1">Confirmar Contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500"
                                autocomplete="new-password" required>
                        </div>
                    </div>

                    <!-- Register Button -->
                    <button type="submit"
                        class="bg-red-500 hover:bg-blue-600 text-white font-semibold rounded-md py-3 px-4 w-full transition duration-300 shadow-md">
                        Registrarse
                    </button>
                </form>
                <!-- Login Link -->
                <div class="mt-6 text-green-500 text-center">
                    <a href="{{ route('client.login') }}" class="hover:underline">¿Ya tienes cuenta? Inicia sesión
                        aquí</a>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('welcome') }}" class="text-gray-500 hover:text-gray-700 text-sm">Volver al
                        inicio</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
