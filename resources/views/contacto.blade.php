@ -0,0 +1,298 @@
@extends('layouts.app')

@section('title', 'Contáctanos')

@section('content')
    <!-- Hero Section con Alto Contraste -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-black" aria-labelledby="contacto-hero-heading">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/fondo_inicio.jpg') }}" alt="" class="hero-bg-img w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/80"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4 text-center text-white">
            <h1 id="contacto-hero-heading" class="text-5xl md:text-7xl font-bold mb-6 leading-tight text-white">
                Ponte en Contacto<br>con UrbanHoops
            </h1>
            <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto text-gray-100 font-medium">
                Estamos aquí para ayudarte con tus preguntas, sugerencias y demás inquietudes.
            </p>
        </div>
    </section>

    <!-- Sección de Contacto Principal -->
    <section id="formulario-contacto" class="py-16 bg-white" aria-labelledby="formulario-heading">
        <div class="container mx-auto px-4">
            <h2 id="formulario-heading" class="text-4xl font-bold text-gray-900 mb-12 text-center">
                Envíanos un Mensaje
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Formulario -->
                <div class="bg-gray-50 p-8 rounded-xl">
                    <form method="POST" action="#" class="space-y-6">
                        @csrf

                        <!-- Campo Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-bold text-gray-900 mb-2">
                                Nombre Completo <span class="text-red-600" aria-label="requerido">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nombre" 
                                name="nombre" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand text-gray-900 bg-white"
                                placeholder="Tu nombre completo"
                                aria-required="true"
                                aria-describedby="nombre-help"
                            >
                            <p id="nombre-help" class="text-gray-600 text-sm mt-1">Ingresa tu nombre completo para poder identificarte correctamente</p>
                        </div>

                        <!-- Campo Email -->
                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-900 mb-2">
                                Correo Electrónico <span class="text-red-600" aria-label="requerido">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand text-gray-900 bg-white"
                                placeholder="tu@email.com"
                                aria-required="true"
                                aria-describedby="email-help"
                            >
                            <p id="email-help" class="text-gray-600 text-sm mt-1">Usaremos este correo para responderte</p>
                        </div>

                        <!-- Campo Teléfono -->
                        <div>
                            <label for="telefono" class="block text-sm font-bold text-gray-900 mb-2">
                                Teléfono <span class="text-gray-500 text-sm">(opcional)</span>
                            </label>
                            <input 
                                type="tel" 
                                id="telefono" 
                                name="telefono" 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand text-gray-900 bg-white"
                                placeholder="+593 99 123 4567"
                                aria-describedby="telefono-help"
                            >
                            <p id="telefono-help" class="text-gray-600 text-sm mt-1">Si prefieres que te llamemos</p>
                        </div>

                        <!-- Campo Asunto -->
                        <div>
                            <label for="asunto" class="block text-sm font-bold text-gray-900 mb-2">
                                Asunto <span class="text-red-600" aria-label="requerido">*</span>
                            </label>
                            <select 
                                id="asunto" 
                                name="asunto" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand text-gray-900 bg-white"
                                aria-required="true"
                                aria-describedby="asunto-help"
                            >
                                <option value="">-- Selecciona un asunto --</option>
                                <option value="consulta_producto">Consulta sobre productos</option>
                                <option value="problema_pedido">Problema con mi pedido</option>
                                <option value="envios">Información de envíos</option>
                                <option value="devolucion">Devoluciones y cambios</option>
                                <option value="sugerencia">Sugerencia o comentario</option>
                                <option value="otro">Otro</option>
                            </select>
                            <p id="asunto-help" class="text-gray-600 text-sm mt-1">Selecciona el tema de tu consulta</p>
                        </div>

                        <!-- Campo Mensaje -->
                        <div>
                            <label for="mensaje" class="block text-sm font-bold text-gray-900 mb-2">
                                Mensaje <span class="text-red-600" aria-label="requerido">*</span>
                            </label>
                            <textarea 
                                id="mensaje" 
                                name="mensaje" 
                                rows="6" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand text-gray-900 bg-white resize-none"
                                placeholder="Cuéntanos tu consulta o problema..."
                                aria-required="true"
                                aria-describedby="mensaje-help"
                            ></textarea>
                            <p id="mensaje-help" class="text-gray-600 text-sm mt-1">Describe detalladamente tu mensaje (mínimo 10 caracteres)</p>
                        </div>

                        <!-- Checkbox Aceptación -->
                        <div class="flex items-start">
                            <input 
                                type="checkbox" 
                                id="aceptacion" 
                                name="aceptacion" 
                                required
                                class="w-5 h-5 border-2 border-gray-300 rounded focus:ring-2 focus:ring-brand mt-1"
                                aria-required="true"
                                aria-describedby="aceptacion-help"
                            >
                            <label for="aceptacion" class="ml-3 text-sm text-gray-700">
                                Acepto la <a href="#" class="text-brand font-bold underline hover:no-underline focus:outline-2 focus:outline-offset-2 focus:outline-brand" target="_blank">política de privacidad</a> y <a href="#" class="text-brand font-bold underline hover:no-underline focus:outline-2 focus:outline-offset-2 focus:outline-brand" target="_blank">términos de servicio</a>
                                <span class="text-red-600" aria-label="requerido">*</span>
                            </label>
                            <p id="aceptacion-help" class="sr-only">Debes aceptar los términos para continuar</p>
                        </div>

                        <!-- Botón Enviar -->
                        <button 
                            type="submit" 
                            class="btn btn-brand w-full text-lg py-3 font-bold transition-all duration-300 focus:outline-2 focus:outline-offset-2 focus:outline-brand"
                            aria-label="Enviar formulario de contacto"
                        >
                            Enviar Mensaje
                        </button>
                    </form>
                </div>

                <!-- Información de Contacto -->
                <div class="space-y-8">
                    <div class="bg-black text-white p-8 rounded-xl">
                        <h3 class="text-2xl font-bold mb-6 text-yellow-400">Información de Contacto</h3>

                        <div class="space-y-6">
                            <!-- Teléfono -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 text-2xl" aria-hidden="true">📞</div>
                                <div>
                                    <h4 class="font-bold text-white text-lg mb-2">Teléfono</h4>
                                    <a href="tel:+593123456789" class="text-yellow-400 font-bold hover:underline focus:outline-2 focus:outline-offset-2 focus:outline-yellow-400">
                                        +593 99 123 4567
                                    </a>
                                    <p class="text-gray-300 text-sm mt-1">Lunes a viernes: 9:00 AM - 6:00 PM</p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 text-2xl" aria-hidden="true">✉️</div>
                                <div>
                                    <h4 class="font-bold text-white text-lg mb-2">Email</h4>
                                    <a href="mailto:contacto@urbanhoops.com" class="text-yellow-400 font-bold hover:underline focus:outline-2 focus:outline-offset-2 focus:outline-yellow-400">
                                        contacto@urbanhoops.com
                                    </a>
                                    <p class="text-gray-300 text-sm mt-1">Respondemos en 24 horas</p>
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 text-2xl" aria-hidden="true">📍</div>
                                <div>
                                    <h4 class="font-bold text-white text-lg mb-2">Ubicación</h4>
                                    <address class="text-gray-300 not-italic">
                                        Quito, Ecuador<br>
                                        Sector La Mariscal
                                    </address>
                                </div>
                            </div>

                            <!-- Horario -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 text-2xl" aria-hidden="true">⏰</div>
                                <div>
                                    <h4 class="font-bold text-white text-lg mb-2">Horario de Atención</h4>
                                    <ul class="text-gray-300 space-y-1">
                                        <li><strong>Lunes a Viernes:</strong> 9:00 AM - 6:00 PM</li>
                                        <li><strong>Sábados:</strong> 10:00 AM - 4:00 PM</li>
                                        <li><strong>Domingos:</strong> Cerrado</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Redes Sociales -->
                    <div class="bg-gray-50 p-8 rounded-xl">
                        <h3 class="text-2xl font-bold mb-6 text-gray-900">Síguenos en Redes Sociales</h3>
                        <div class="flex gap-4 flex-wrap">
                            <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white font-bold rounded-lg hover:bg-gray-800 focus:outline-2 focus:outline-offset-2 focus:outline-brand transition-colors" aria-label="Síguenos en Facebook">
                                <span aria-hidden="true">f</span> Facebook
                            </a>
                            <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white font-bold rounded-lg hover:bg-gray-800 focus:outline-2 focus:outline-offset-2 focus:outline-brand transition-colors" aria-label="Síguenos en Instagram">
                                <span aria-hidden="true">📷</span> Instagram
                            </a>
                            <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white font-bold rounded-lg hover:bg-gray-800 focus:outline-2 focus:outline-offset-2 focus:outline-brand transition-colors" aria-label="Síguenos en Twitter">
                                <span aria-hidden="true">𝕏</span> Twitter
                            </a>
                            <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white font-bold rounded-lg hover:bg-gray-800 focus:outline-2 focus:outline-offset-2 focus:outline-brand transition-colors" aria-label="Síguenos en TikTok">
                                <span aria-hidden="true">♪</span> TikTok
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Preguntas Frecuentes -->
    <section id="preguntas-frecuentes" class="py-16 bg-gray-50" aria-labelledby="faq-heading">
        <div class="container mx-auto px-4">
            <h2 id="faq-heading" class="text-4xl font-bold text-gray-900 mb-12 text-center">
                Preguntas Frecuentes
            </h2>

            <div class="max-w-3xl mx-auto space-y-4">
                <!-- FAQ Item 1 -->
                <details class="bg-white p-6 rounded-lg shadow-md border-2 border-gray-300 group">
                    <summary class="font-bold text-lg text-gray-900 cursor-pointer flex justify-between items-center hover:text-brand focus:outline-2 focus:outline-offset-2 focus:outline-brand" tabindex="0">
                        ¿Cuál es el tiempo de envío?
                        <span aria-hidden="true" class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-4">Los envíos se realizan dentro de 2-3 días hábiles. Puedes elegir entre envío estándar (5-7 días) o express (2-3 días).</p>
                </details>

                <!-- FAQ Item 2 -->
                <details class="bg-white p-6 rounded-lg shadow-md border-2 border-gray-300 group">
                    <summary class="font-bold text-lg text-gray-900 cursor-pointer flex justify-between items-center hover:text-brand focus:outline-2 focus:outline-offset-2 focus:outline-brand" tabindex="0">
                        ¿Cómo hago una devolución?
                        <span aria-hidden="true" class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-4">Tienes 30 días para devolver cualquier producto en perfecto estado. Contacta a nuestro equipo de atención al cliente y te proporcionaremos la etiqueta de envío.</p>
                </details>

                <!-- FAQ Item 3 -->
                <details class="bg-white p-6 rounded-lg shadow-md border-2 border-gray-300 group">
                    <summary class="font-bold text-lg text-gray-900 cursor-pointer flex justify-between items-center hover:text-brand focus:outline-2 focus:outline-offset-2 focus:outline-brand" tabindex="0">
                        ¿Qué métodos de pago aceptan?
                        <span aria-hidden="true" class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-4">Aceptamos tarjetas de crédito/débito, transferencia bancaria, PayPal y billeteras digitales como Apple Pay y Google Pay.</p>
                </details>

                <!-- FAQ Item 4 -->
                <details class="bg-white p-6 rounded-lg shadow-md border-2 border-gray-300 group">
                    <summary class="font-bold text-lg text-gray-900 cursor-pointer flex justify-between items-center hover:text-brand focus:outline-2 focus:outline-offset-2 focus:outline-brand" tabindex="0">
                        ¿Tienen tienda física?
                        <span aria-hidden="true" class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-4">Sí, contamos con una tienda en La Mariscal, Quito. Te invitamos a visitarnos de lunes a sábado.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- Llamada a Acción Final -->
    <section class="py-16 bg-black text-white text-center">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold mb-6 text-yellow-400">¿Aún tienes dudas?</h2>
            <p class="text-xl text-gray-200 mb-8 max-w-2xl mx-auto">
                Nuestro equipo de soporte está disponible para ayudarte. No dudes en contactarnos.
            </p>
            <a href="#formulario-contacto" class="btn btn-outline-brand px-8 py-3 text-lg font-bold inline-block focus:outline-2 focus:outline-offset-2 focus:outline-yellow-400">
                Ir al Formulario
            </a>
        </div>
    </section>
@endsection