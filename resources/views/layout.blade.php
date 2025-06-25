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
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-attachment: fixed;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="0.5" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
            pointer-events: none;
            z-index: -1;
        }

        .installer-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .installer-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 860px;
            /* Aumentado ligeramente */
            width: 100%;
        }

        .installer-header {
            background: linear-gradient(135deg, #FF512F 0%, #F09819 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 30px;
            text-align: center;
        }

        .installer-body {
            padding: 40px;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            position: relative;
        }

        .step.completed {
            background: #28a745;
            color: white;
        }

        .step.active {
            background: #FF512F;
            color: white;
            animation: pulse 2s infinite;
        }

        .step:not(.completed):not(.active) {
            background: #e9ecef;
            color: #6c757d;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 20px;
            height: 2px;
            background: #e9ecef;
            transform: translateY(-50%);
        }

        .step.completed:not(:last-child)::after {
            background: #28a745;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .btn-primary {
            background: linear-gradient(135deg, #FF512F 0%, #F09819 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 81, 47, 0.3);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #FF512F;
            box-shadow: 0 0 0 0.2rem rgba(255, 81, 47, 0.25);
        }

        .alert {
            border-radius: 15px;
            border: none;
        }

        .installation-step {
            padding: 10px 0;
            font-size: 16px;
            color: #495057;
        }

        .fa-spin {
            color: #FF512F;
        }

        .progress {
            border-radius: 15px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 15px;
            background: linear-gradient(135deg, #FF512F 0%, #F09819 100%);
        }

        hr {
            border: 0;
            height: 1px;
            background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0));
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>

    @stack('styles')
    <style>
        /* Estilos adicionales para el layout horizontal */
        .installer-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .installer-sidebar {
            width: 320px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px 25px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
        }

        .installer-sidebar .logo {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(135deg, #FF512F 0%, #F09819 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 35px;
            padding: 20px 15px;
            border-radius: 15px;
            background-color: rgba(255, 81, 47, 0.05);
            border: 2px solid rgba(255, 81, 47, 0.1);
            position: relative;
            overflow: hidden;
        }

        .installer-sidebar .logo::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .installer-sidebar .logo:hover::before {
            left: 100%;
        }

        .installer-content-area {
            flex-grow: 1;
            padding: 40px 50px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            margin-left: 320px;
            overflow-y: auto;
            border-radius: 25px 0 0 25px;
            box-shadow: inset 0 0 30px rgba(0, 0, 0, 0.05);
        }

        /* Estilos para el indicador de pasos vertical (se adaptarán en el siguiente paso) */
        .step-indicator-vertical {
            list-style: none;
            padding-left: 0;
            margin-top: 20px;
        }

        .step-indicator-vertical .step {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            background-color: #e9ecef;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
        }

        .step-indicator-vertical .step .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
            background-color: #adb5bd;
            color: white;
        }

        .step-indicator-vertical .step .step-label {
            flex-grow: 1;
        }

        .step-indicator-vertical .step.active {
            background-color: #ffe8e0;
            /* Naranja claro */
            color: #FF512F;
            border-left-color: #FF512F;
        }

        .step-indicator-vertical .step.active .step-number {
            background-color: #FF512F;
        }

        .step-indicator-vertical .step.completed {
            background-color: #e6f7ee;
            /* Verde claro */
            color: #28a745;
            border-left-color: #28a745;
        }

        .step-indicator-vertical .step.completed .step-number {
            background-color: #28a745;
        }

        .installer-main-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .installer-main-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
        }

        .installer-main-header p {
            font-size: 0.95rem;
            color: #6c757d;
        }

        /* Responsive: colapsar sidebar en pantallas pequeñas */
        @media (max-width: 768px) {
            .installer-sidebar {
                width: 100%;
                height: auto;
                position: static;
                /* Cambiar a estático para apilar */
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                padding-bottom: 10px;
            }

            .installer-sidebar .logo {
                margin-bottom: 15px;
                padding-bottom: 15px;
                font-size: 1.25rem;
            }

            .installer-content-area {
                margin-left: 0;
                padding: 20px;
            }

            .step-indicator-vertical {
                /* Convertir a horizontal en móvil */
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                margin-top: 10px;
            }

            .step-indicator-vertical .step {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 8px;
                margin: 5px;
                border-left: none;
                border-bottom: 3px solid transparent;
                min-width: 70px;
                /* Ancho mínimo para cada paso */
                max-width: 90px; /* Para controlar la expansión si el texto es muy largo */
                overflow: hidden; /* Evitar que el contenido desborde el contenedor del paso */
            }

            .step-indicator-vertical .step .step-number {
                margin-right: 0;
                margin-bottom: 5px;
            }

            .step-indicator-vertical .step .step-label {
                word-wrap: break-word; /* Permite que las palabras largas se dividan */
                overflow-wrap: break-word; /* Estándar actual para word-wrap */
                -webkit-hyphens: auto; /* Para navegadores WebKit */
                -ms-hyphens: auto; /* Para IE/Edge */
                hyphens: auto; /* Estándar */
                line-height: 1.25; /* Ajustar espaciado para texto envuelto */
                font-size: 0.8rem; /* Reducir un poco el tamaño para mejor ajuste */
                display: inline-block; /* Para que el text-align:center funcione bien con múltiples líneas */
            }

            .step-indicator-vertical .step.active {
                border-bottom-color: #FF512F;
            }

            .step-indicator-vertical .step.completed {
                border-bottom-color: #28a745;
            }

            .installer-main-header h1 {
                font-size: 1.5rem;
            }
        }

        /* Animaciones mejoradas */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .installer-content-area {
            animation: slideInUp 0.6s ease-out;
        }

        .installer-sidebar {
            animation: slideInUp 0.8s ease-out;
        }

        /* Efectos hover mejorados */
        .btn-primary {
            position: relative;
            overflow: hidden;
            transform: translateY(0);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 81, 47, 0.4);
        }

        /* Cards mejoradas */
        .card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        /* Form controls mejorados */
        .form-control {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(255, 81, 47, 0.15);
        }

        /* Alerts mejoradas */
        .alert {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-color: rgba(40, 167, 69, 0.2);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.2);
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border-color: rgba(255, 193, 7, 0.2);
        }

        .alert-info {
            background: rgba(13, 202, 240, 0.1);
            border-color: rgba(13, 202, 240, 0.2);
        }
    </style>
</head>

<body>
    <div class="installer-wrapper">
        <aside class="installer-sidebar">
            <div class="logo">
                <i class="fas fa-rocket"></i> Laravel Installer
            </div>
            @yield('steps') <!-- Aquí irá el nuevo indicador de pasos vertical -->
        </aside>

        <main class="installer-content-area">
            <div class="installer-main-header">
                <h1>@yield('header', 'Proceso de Instalación')</h1>
                <p>@yield('description', 'Siga los pasos para configurar su aplicación.')</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Se encontraron errores:</h5>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="content-body mt-3">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>