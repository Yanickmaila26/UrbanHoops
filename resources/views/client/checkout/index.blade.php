@extends('layouts.app')

@section('title', 'Finalizar Compra')

@section('content')
    <section class="container py-12">
        <div class="row">
            <!-- Billing & Shipping Form -->
            <div class="col-lg-8 mb-4">
                <h2 class="text-3xl font-bold mb-6 font-poppins text-gray-900 border-b pb-2">Información de Envío y Pago</h2>

                <form id="checkoutForm" method="POST" action="{{ route('client.checkout.process') }}">
                    @csrf
                    <input type="hidden" name="cart_items" id="cart_items">
                    <input type="hidden" name="total" id="total_amount">

                    <!-- Saved Profiles Selector -->
                    @if ($savedProfiles->count() > 0)
                        <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Usar datos guardados:</label>
                            <select id="savedProfileSelector" class="form-select w-full bg-white border-blue-200">
                                <option value="">-- Nueva Dirección / Tarjeta --</option>
                                @foreach ($savedProfiles as $profile)
                                    <option value="{{ $profile->DAF_Codigo }}"
                                        data-direccion="{{ $profile->DAF_Direccion }}"
                                        data-ciudad="{{ $profile->DAF_Ciudad }}" data-estado="{{ $profile->DAF_Estado }}"
                                        data-cp="{{ $profile->DAF_CP }}"
                                        data-card-expiry="{{ $profile->DAF_Tarjeta_Expiracion }}">
                                        {{ $profile->DAF_Direccion }} - {{ $profile->DAF_Ciudad }} (Termina en ****)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Address Section -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-6">
                        <h3 class="text-xl font-bold mb-4 text-brand">📍 Dirección de Envío y Facturación</h3>

                        <div class="mb-3">
                            <label for="direccion" class="form-label font-bold text-sm">Dirección Completa</label>
                            <input type="text" class="form-control" id="direccion" name="direccion"
                                placeholder="Calle principal, nro, intersección" required
                                value="{{ old('direccion', $cliente->CLI_Direccion ?? '') }}">
                        </div>

                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="ciudad" class="form-label font-bold text-sm">Ciudad</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" required
                                    value="{{ old('ciudad') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label font-bold text-sm">Estado/Provincia</label>
                                <input type="text" class="form-control" id="estado" name="estado" required
                                    value="{{ old('estado') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="cp" class="form-label font-bold text-sm">Código Postal</label>
                                <input type="text" class="form-control" id="cp" name="cp" required
                                    value="{{ old('cp') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold mb-4 text-brand">💳 Método de Pago</h3>

                        <div class="alert alert-info text-sm mb-4">
                            <i class="bi bi-shield-lock-fill"></i> Sus datos son encriptados y procesados de forma segura.
                        </div>

                        <div class="mb-3">
                            <label for="card_number" class="form-label font-bold text-sm">Número de Tarjeta</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                <input type="text" class="form-control" id="card_number" name="card_number"
                                    placeholder="0000 0000 0000 0000" maxlength="19" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="card_expiry" class="form-label font-bold text-sm">Expiración (MM/YY)</label>
                                <input type="text" class="form-control" id="card_expiry" name="card_expiry"
                                    placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="card_cvv" class="form-label font-bold text-sm">CVV</label>
                                <input type="password" class="form-control" id="card_cvv" name="card_cvv" placeholder="123"
                                    maxlength="4" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 text-end">
                        <button type="submit" id="btnPlaceOrder"
                            class="btn btn-success btn-lg font-bold w-100 py-3 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            🔒 Confirmar y Pagar
                        </button>
                        <p class="mt-2 text-xs text-muted text-center">Al confirmar, usted acepta nuestros términos y
                            condiciones.</p>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 sticky top-24">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2">Resumen del Pedido</h3>

                    <div id="checkoutCartItems" class="space-y-4 mb-4" style="max-height: 300px; overflow-y: auto;">
                        <!-- Items injected by JS -->
                        <div class="text-center text-muted py-4">
                            <div class="spinner-border text-brand" role="status"></div>
                            <p class="mt-2 text-xs">Cargando carrito...</p>
                        </div>
                    </div>

                    <div class="border-t pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span id="summarySubtotal">$0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>IVA ({{ config('urbanhoops.iva', 15) }}%)</span>
                            <span id="summaryIVA">$0.00</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-gray-900 border-t pt-2 mt-2">
                            <span>Total a Pagar</span>
                            <span id="summaryTotal" class="text-brand">$0.00</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('client.cart') }}"
                            class="text-sm text-brand font-bold hover:underline block text-center">
                            <i class="bi bi-pencil"></i> Editar Carrito
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Logic Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartItemsContainer = document.getElementById('checkoutCartItems');
            const summarySubtotal = document.getElementById('summarySubtotal');
            const summaryIVA = document.getElementById('summaryIVA');
            const summaryTotal = document.getElementById('summaryTotal');
            const checkoutForm = document.getElementById('checkoutForm');

            // Profile Selector Logic
            const profileSelector = document.getElementById('savedProfileSelector');
            if (profileSelector) {
                profileSelector.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const direccion = document.getElementById('direccion');
                    const ciudad = document.getElementById('ciudad');
                    const estado = document.getElementById('estado');
                    const cp = document.getElementById('cp');
                    const cardExpiry = document.getElementById('card_expiry');
                    // Note: Card Number and CVV are encrypted, so we cannot autofill them from frontend safely/easily
                    // without fetching decrypted version via AJAX which might be insecure or intended behavior.
                    // For now, let's autofill address data which is plain text.
                    // If the user wants to reuse the card, usually tokens are used.
                    // Since we store encrypted, we might need to ask them to re-enter sensitive info or handle it in backend.
                    // Re-entering Card Number is safer if we don't have tokens.

                    if (this.value) {
                        direccion.value = selectedOption.dataset.direccion;
                        ciudad.value = selectedOption.dataset.ciudad;
                        estado.value = selectedOption.dataset.estado;
                        cp.value = selectedOption.dataset.cp;
                        cardExpiry.value = selectedOption.dataset.cardExpiry;

                        // Highlight that they need to re-enter sensitive info if empty
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Datos de dirección cargados. Por favor confirma tu tarjeta.',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        direccion.value = '';
                        ciudad.value = '';
                        estado.value = '';
                        cp.value = '';
                        cardExpiry.value = '';
                    }
                });
            }

            // Retrieve cart items via global CartModel (assuming it exposes methods or we access storage directly)
            // Since CartModelSimple uses 'uh_cart_v1', we can read it.
            const cartData = JSON.parse(localStorage.getItem('uh_cart_v1')) || [];

            if (cartData.length === 0) {
                window.location.href = "{{ route('client.cart') }}"; // Redirect if empty
                return;
            }

            // Render Cart Summary
            let subtotal = 0;
            let html = '';

            cartData.forEach(item => {
                subtotal += item.price * item.qty;
                html += `
                <div class="flex gap-3">
                    <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded shadow-sm bg-gray-100">
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-800 line-clamp-2">${item.name}</h4>
                        <div class="flex justify-between items-end mt-1">
                            <span class="text-xs text-gray-500">Cant: ${item.qty}</span>
                            <span class="text-sm font-semibold text-gray-900">$${(item.price * item.qty).toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
            });

            cartItemsContainer.innerHTML = html;

            // Calculate Totals
            const ivaRate = {{ config('urbanhoops.iva', 15) }} / 100;
            const iva = subtotal * ivaRate;
            const total = subtotal + iva;

            summarySubtotal.textContent = `$${subtotal.toFixed(2)}`;
            summaryIVA.textContent = `$${iva.toFixed(2)}`;
            summaryTotal.textContent = `$${total.toFixed(2)}`;

            // Populate hidden fields
            document.getElementById('cart_items').value = JSON.stringify(cartData);
            document.getElementById('total_amount').value = total.toFixed(2);

            // Handle Form Submission with AJAX
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const btn = document.getElementById('btnPlaceOrder');
                const originalText = btn.innerHTML;
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
                btn.disabled = true;

                const formData = new FormData(checkoutForm);

                fetch(checkoutForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear Cart
                            localStorage.removeItem('uh_cart_v1');
                            // Trigger event to update other components if any
                            window.dispatchEvent(new Event('storage'));
                            // Update UI Badge (if visible)
                            const badge = document.getElementById('cartCount');
                            if (badge) {
                                badge.innerText = '0';
                                badge.style.display = 'none';
                            }

                            // Show success alert
                            Swal.fire({
                                title: '¡Pago Exitoso!',
                                text: 'Tu pedido ha sido procesado correctamente.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect_url;
                            });
                        } else {
                            throw new Error(data.message || 'Error desconocido');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: error.message ||
                                'Hubo un problema al procesar tu pago. Por favor intenta nuevamente.',
                            icon: 'error',
                            confirmButtonText: 'Entendido'
                        });
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
            });

            // Input Formatting
            document.getElementById('card_number').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
            });

            document.getElementById('card_expiry').addEventListener('input', function(e) {
                let input = e.target.value.replace(/\D/g, '');
                if (input.length > 2) input = input.substring(0, 2) + '/' + input.substring(2);
                e.target.value = input;
            });
        });
    </script>
@endsection
