<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Clientes - UrbanHoops</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <!-- component -->
    <div class="bg-sky-100 flex justify-center items-center h-screen">
        <!-- Left: Image -->
        <div class="w-1/2 h-screen hidden lg:block">
            <img src="https://img.freepik.com/fotos-premium/imagen-fondo_910766-187.jpg?w=826" alt="UrbanHoops Background"
                class="object-cover w-full h-full">
        </div>
        <!-- Right: Login Form -->
        <div class="lg:p-36 md:p-52 sm:20 p-8 w-full lg:w-1/2">
            <h1 class="text-2xl font-semibold mb-4 text-center lg:text-left">Login Clientes</h1>
            <form action="{{ route('client.login.submit') }}" method="POST">
                @csrf
                <!-- Username Input (Email) -->
                <div class="mb-4">
                    <label for="username" class="block text-gray-600">Email / Usuario</label>
                    <input type="email" id="username" name="username" value="{{ old('username') }}"
                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500"
                        autocomplete="email" required>
                    @error('username')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Password Input -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-800">Contraseña</label>
                    <input type="password" id="password" name="password"
                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500"
                        autocomplete="current-password" required>
                </div>
                <!-- Remember Me Checkbox -->
                <div class="mb-4 flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="text-red-500">
                    <label for="remember" class="text-green-900 ml-2">Recordarme</label>
                </div>
                <!-- Forgot Password Link -->
                <div class="mb-6 text-blue-500">
                    <a href="#" class="hover:underline">¿Olvidaste tu contraseña?</a>
                </div>
                <!-- Login Button -->
                <button type="submit"
                    class="bg-red-500 hover:bg-blue-600 text-white font-semibold rounded-md py-2 px-4 w-full transition duration-300">
                    Iniciar Sesión
                </button>
            </form>
            <!-- Sign up Link -->
            <div class="mt-6 text-green-500 text-center">
                <a href="#" class="hover:underline">Regístrate aquí</a>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('welcome') }}" class="text-gray-500 hover:text-gray-700 text-sm">Volver al inicio</a>
            </div>
        </div>
    </div>
</body>

</html>
