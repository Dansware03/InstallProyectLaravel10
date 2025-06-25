<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel Installer')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f0f2f5; /* Un gris claro, típico de fondos de SO */
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Fuente común en Windows */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333; /* Color de texto principal */
        }

        .installer-window {
            width: 100%;
            max-width: 850px; /* Ancho de la ventana del instalador */
            background-color: #ffffff;
            border: 1px solid #c5c5c5; /* Borde sutil como una ventana */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); /* Sombra de ventana */
            border-radius: 6px; /* Bordes ligeramente redondeados */
            display: flex;
            flex-direction: column;
            min-height: 600px; /* Altura mínima para dar sensación de aplicación */
        }

        .installer-title-bar {
            background-color: #e9e9e9; /* Barra de título grisácea */
            padding: 8px 12px;
            border-bottom: 1px solid #c5c5c5;
            border-radius: 6px 6px 0 0;
            text-align: center; /* Centrar el título del instalador */
            font-weight: bold;
            color: #333;
        }

        .installer-progress-bar-container {
            padding: 20px 25px;
            border-bottom: 1px solid #e0e0e0;
            background-color: #f8f9fa;
        }

        .installer-steps-display {
            display: flex;
            justify-content: space-around; /* Distribuir los pasos */
            font-size: 0.9rem;
            color: #6c757d;
        }
        .installer-steps-display .step-item {
            padding: 8px 10px; /* Aumentado padding vertical */
            border-bottom: 2px solid transparent;
            line-height: 1.4; /* Ajustado para mejor espaciado vertical */
            text-align: center; /* Centrar texto si se envuelve */
        }
        .installer-steps-display .step-item.active {
            color: #007bff; /* Color primario para el paso activo */
            border-bottom-color: #007bff;
            font-weight: bold;
        }
         .installer-steps-display .step-item.completed {
            color: #28a745; /* Color verde para pasos completados */
        }


        .installer-content-area {
            padding: 25px 30px;
            flex-grow: 1;
            overflow-y: auto; /* Scroll si el contenido es muy largo */
        }

        .installer-main-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .installer-main-header h1 {
            font-size: 1.5rem; /* Un poco más pequeño para estética de instalador */
            font-weight: 600;
            color: #333;
        }

        .installer-main-header p {
            font-size: 0.9rem;
            color: #555;
        }

        .content-body {
            /* Estilos para el cuerpo del contenido si son necesarios */
        }

        .installer-footer-actions {
            padding: 15px 30px;
            border-top: 1px solid #e0e0e0;
            background-color: #f8f9fa;
            display: flex;
            justify-content: flex-end; /* Botones a la derecha */
            gap: 10px; /* Espacio entre botones */
        }

        /* Estilos para botones que emulen un instalador de SO */
        .btn-installer {
            padding: 8px 20px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
            border: 1px solid #adadad;
            background-color: #f0f0f0;
            color: #333;
            transition: background-color 0.2s ease;
        }
        .btn-installer:hover {
            background-color: #e0e0e0;
            border-color: #939393;
        }
        .btn-installer-primary {
            background-color: #0078d4; /* Azul de Windows */
            border-color: #005a9e;
            color: white;
        }
        .btn-installer-primary:hover {
            background-color: #005a9e;
            border-color: #004c87;
        }

        /* Ajustes generales para Bootstrap dentro del instalador */
        .alert {
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .form-control, .form-select {
            border-radius: 4px;
            font-size: 0.9rem;
            border-color: #ced4da;
        }
        .form-control:focus, .form-select:focus {
            border-color: #86b7fe; /* Color de foco de Bootstrap */
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .list-group-item {
            font-size: 0.9rem;
        }
        code {
            background-color: #e9ecef;
            padding: .2em .4em;
            border-radius: .25rem;
            font-size: 85%;
        }
        .progress {
             border-radius: 4px;
        }
        .progress-bar {
            background-color: #0078d4; /* Azul para progreso */
        }
        .card { /* Para la tarjeta de credenciales, etc. */
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }

    </style>
    @stack('styles')
</head>

<body>
    <div class="installer-window">
        <div class="installer-title-bar">
            Laravel Installer
        </div>

        <!-- Nueva sección para la barra de progreso/pasos -->
        <div class="installer-progress-bar-container">
            @if(isset($stepsData) && !empty($stepsData['total']))
                <div class="installer-steps-display">
                    @foreach($stepsData['total'] as $step)
                        <span class="step-item {{ $step['status'] ?? 'pending' }}">
                            {{ $step['name'] }}
                        </span>
                    @endforeach
                </div>
            @else
                @yield('progress-steps') {{-- Fallback por si alguna vista no usa la nueva estructura --}}
            @endif
        </div>

        <main class="installer-content-area">
            <div class="installer-main-header">
                <h1>@yield('header', 'Proceso de Instalación')</h1>
                <p>@yield('description', 'Siga los pasos para configurar su aplicación.')</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading mb-1" style="font-size: 1rem;"><i class="fas fa-times-circle me-2"></i>Se encontraron errores:</h5>
                    <ul class="mb-0 small ps-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="content-body mt-3">
                @yield('content')
            </div>
        </main>

        <footer class="installer-footer-actions">
            @yield('footer-actions')
        </footer>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>