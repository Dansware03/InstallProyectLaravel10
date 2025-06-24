@extends('installer::layout')

@section('title', 'Instalando - Instalación Rápida')

@section('header', 'Instalación en Progreso')

@section('description', 'Por favor espere mientras configuramos su aplicación')

@section('steps')
    <div class="step-indicator">
        <div class="step completed">1</div>
        <div class="step completed">2</div>
        <div class="step active">3</div>
    </div>
@endsection

@section('content')
<div class="text-center">
    <div class="mb-4">
        <i class="fas fa-cogs fa-spin" style="font-size: 4rem; color: #FF512F;"></i>
    </div>

    <h3 class="mb-4">Configurando su aplicación...</h3>

    <div class="progress mb-4" style="height: 30px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
    </div>

    <div id="installation-status" class="mb-4">
        <p class="lead">Inicializando instalación...</p>
    </div>

    <div id="installation-steps" class="text-start">
        <div class="installation-step" data-step="database">
            <i class="fas fa-circle-notch fa-spin me-2"></i>
            <span>Configurando base de datos...</span>
        </div>
    </div>

    <div id="installation-complete" style="display: none;">
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>¡Instalación completada exitosamente!</strong>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-key me-2"></i>Credenciales de Acceso</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Email:</strong>
                        <div class="input-group mt-1">
                            <input type="text" class="form-control" id="admin-email" readonly>
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="copyToClipboard('admin-email')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong>Contraseña:</strong>
                        <div class="input-group mt-1">
                            <input type="text" class="form-control" id="admin-password" readonly>
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="copyToClipboard('admin-password')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Importante:</strong> Guarde estas credenciales en un lugar seguro.
                    Se recomienda cambiar la contraseña después del primer acceso.
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="/login" class="btn btn-primary btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i>Ir al Login
            </a>
        </div>
    </div>

    <div id="installation-error" style="display: none;">
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error durante la instalación:</strong>
            <div id="error-message"></div>
        </div>

        <div class="mt-4">
            <a href="{{ route('installer.welcome') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
            </a>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const progressBar = document.querySelector('.progress-bar');
            const statusElement = document.getElementById('installation-status');
            const stepsContainer = document.getElementById('installation-steps');
            const completeSection = document.getElementById('installation-complete');
            const errorSection = document.getElementById('installation-error');

            let currentStep = 0;
            const steps = [
                { key: 'database', text: 'Configurando base de datos...', progress: 20 },
                { key: 'migrations', text: 'Ejecutando migraciones...', progress: 40 },
                { key: 'security', text: 'Aplicando configuraciones de seguridad...', progress: 60 },
                { key: 'optimizations', text: 'Aplicando optimizaciones...', progress: 80 },
                { key: 'user', text: 'Creando usuario administrador...', progress: 95 },
                { key: 'complete', text: 'Finalizando instalación...', progress: 100 }
            ];

            function updateProgress() {
                if (currentStep < steps.length) {
                    const step = steps[currentStep];

                    // Actualizar barra de progreso
                    progressBar.style.width = step.progress + '%';

                    // Actualizar estado
                    statusElement.innerHTML = `<p class="lead">${step.text}</p>`;

                    // Actualizar pasos
                    updateStepDisplay();

                    currentStep++;

                    if (currentStep < steps.length) {
                        setTimeout(updateProgress, 1500); // Simular tiempo de procesamiento
                    } else {
                        // Ejecutar instalación real
                        executeInstallation();
                    }
                }
            }

            function updateStepDisplay() {
                const currentStepData = steps[currentStep];
                stepsContainer.innerHTML = `
                <div class="installation-step" data-step="${currentStepData.key}">
                    <i class="fas fa-circle-notch fa-spin me-2"></i>
                    <span>${currentStepData.text}</span>
                </div>
            `;
            }

            function executeInstallation() {
                // Hacer petición AJAX real para ejecutar la instalación
                fetch('{{ route("installer.quick.execute") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccess(data.credentials);
                        } else {
                            showError(data.message);
                        }
                    })
                    .catch(error => {
                        showError('Error de conexión: ' + error.message);
                    });
            }

            function showSuccess(credentials) {
                progressBar.style.width = '100%';
                statusElement.innerHTML = '<p class="lead text-success">¡Instalación completada!</p>';
                stepsContainer.style.display = 'none';

                // Mostrar credenciales
                document.getElementById('admin-email').value = credentials.email;
                document.getElementById('admin-password').value = credentials.password;

                completeSection.style.display = 'block';
            }

            function showError(message) {
                stepsContainer.style.display = 'none';
                document.getElementById('error-message').textContent = message;
                errorSection.style.display = 'block';
            }

            // Función para copiar al portapapeles
            window.copyToClipboard = function (elementId) {
                const element = document.getElementById(elementId);
                element.select();
                element.setSelectionRange(0, 99999);
                document.execCommand('copy');

                // Mostrar feedback
                const button = element.nextElementSibling;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-secondary');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-secondary');
                }, 2000);
            };

            // Iniciar el proceso
            setTimeout(updateProgress, 1000);
        });
    </script>
@endpush