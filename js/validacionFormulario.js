// js/validacionFormulario.js
class ValidacionFormulario {
    constructor() {
        this.form = document.getElementById('formContacto');
        this.init();
    }

    init() {
        if (!this.form) return;

        // Evento de submit
        this.form.addEventListener('submit', (e) => this.validarFormulario(e));

        // Validación en tiempo real
        this.form.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('blur', () => this.validarCampo(input));
            input.addEventListener('input', () => this.limpiarError(input));
        });
    }

    // Expresiones regulares
    get patrones() {
        return {
            nombre: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/,
            email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
            mensaje: /^[\s\S]{10,1000}$/
        };
    }

    // Validar un campo individual
    validarCampo(campo) {
        const valor = campo.value.trim();
        const id = campo.id;
        let valido = true;
        let mensaje = '';

        switch (id) {
            case 'nombre':
                if (!this.patrones.nombre.test(valor)) {
                    valido = false;
                    mensaje = 'El nombre debe tener entre 2 y 50 caracteres (solo letras y espacios)';
                }
                break;

            case 'email':
                if (!this.patrones.email.test(valor)) {
                    valido = false;
                    mensaje = 'Por favor ingresa un email válido (ejemplo: usuario@correo.com)';
                }
                break;

            case 'mensaje':
                if (!this.patrones.mensaje.test(valor)) {
                    valido = false;
                    mensaje = 'El mensaje debe tener entre 10 y 1000 caracteres';
                }
                break;
        }

        if (!valido) {
            this.mostrarError(campo, mensaje);
        } else {
            this.limpiarError(campo);
        }

        return valido;
    }

    // Limpiar error de un campo
    limpiarError(campo) {
        campo.classList.remove('is-invalid');
        campo.classList.add('is-valid');
        const errorElement = document.getElementById(`${campo.id}Error`);
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }

    // Mostrar error en un campo
    mostrarError(campo, mensaje) {
        campo.classList.remove('is-valid');
        campo.classList.add('is-invalid');
        const errorElement = document.getElementById(`${campo.id}Error`);
        if (errorElement) {
            errorElement.textContent = mensaje;
            errorElement.style.display = 'block';
        }
    }

    // Validar todo el formulario
    validarFormulario(event) {
        event.preventDefault();

        const campos = [
            document.getElementById('nombre'),
            document.getElementById('email'),
            document.getElementById('mensaje')
        ];

        let formularioValido = true;

        // Validar cada campo
        campos.forEach(campo => {
            if (!this.validarCampo(campo)) {
                formularioValido = false;
            }
        });

        if (formularioValido) {
            this.enviarFormulario();
        } else {
            this.mostrarMensaje('error', 'Por favor, corrige los errores en el formulario');
        }
    }

    // Enviar formulario (simulado)
    enviarFormulario() {
        const formData = new FormData(this.form);
        const datos = Object.fromEntries(formData);

        console.log('Datos del formulario:', datos);

        // Mostrar estado de carga
        this.mostrarEstado('loading');

        // Simular envío a servidor (en producción sería una petición fetch/axios)
        setTimeout(() => {
            // Simular éxito (90%) o error (10%)
            const exito = Math.random() > 0.1;

            if (exito) {
                this.mostrarEstado('success');
                this.form.reset();

                // Limpiar clases de validación
                this.form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                    el.classList.remove('is-valid', 'is-invalid');
                });

                // Restaurar botón después de 3 segundos
                setTimeout(() => {
                    this.mostrarEstado('none');
                }, 3000);
            } else {
                this.mostrarEstado('error');

                // Restaurar botón después de 3 segundos
                setTimeout(() => {
                    this.mostrarEstado('none');
                }, 3000);
            }
        }, 1500);
    }

    // Mostrar diferentes estados del formulario
    mostrarEstado(estado) {
        const estados = ['loading', 'success', 'error'];

        // Ocultar todos los estados
        estados.forEach(e => {
            const elemento = document.getElementById(`form${e.charAt(0).toUpperCase() + e.slice(1)}`);
            if (elemento) elemento.classList.add('d-none');
        });

        // Deshabilitar/habilitar botón
        const botonSubmit = this.form.querySelector('button[type="submit"]');
        const textoBoton = document.getElementById('submitText');

        if (estado === 'loading') {
            botonSubmit.disabled = true;
            if (textoBoton) textoBoton.textContent = 'Enviando...';
        } else if (estado === 'none') {
            botonSubmit.disabled = false;
            if (textoBoton) textoBoton.textContent = 'Enviar Solicitud';
        }

        // Mostrar estado actual
        if (estado !== 'none') {
            const elementoEstado = document.getElementById(`form${estado.charAt(0).toUpperCase() + estado.slice(1)}`);
            if (elementoEstado) {
                elementoEstado.classList.remove('d-none');
            }
        }
    }

    // Mostrar mensaje temporal
    mostrarMensaje(tipo, mensaje) {
        // Crear elemento de mensaje
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${tipo === 'error' ? 'danger' : 'warning'} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insertar antes del formulario
        this.form.parentNode.insertBefore(alertDiv, this.form);

        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    const validacionFormulario = new ValidacionFormulario();
});