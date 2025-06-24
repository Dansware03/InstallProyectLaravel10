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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            max-width: 800px;
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
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
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
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="installer-container">
        <div class="installer-card">
            <div class="installer-header">
                <h1 class="mb-2">@yield('header', 'Laravel Installer')</h1>
                <p class="mb-0 opacity-75">@yield('description', 'Asistente de instalación para Laravel')</p>
                @yield('steps')
            </div>
            
            <div class="installer-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Se encontraron los siguientes errores:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>