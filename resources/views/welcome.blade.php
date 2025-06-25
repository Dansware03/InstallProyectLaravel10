@extends('installer::layout')

@section('title', 'Bienvenido al Asistente de Instalación')

@section('header', 'Bienvenido')

@section('description', 'Seleccione el tipo de instalación que desea realizar para configurar su aplicación Laravel.')

{{-- Esta vista no tiene un indicador de progreso de pasos numerados --}}

@section('content')
    <div class="text-center mb-4">
        {{-- Icono principal o logo del instalador podría ir aquí si se desea --}}
        {{-- <i class="fas fa-cogs fa-3x text-muted mb-3"></i> --}}
         <p class="lead" style="font-size: 1.1rem;">
            Este asistente le guiará a través del proceso de configuración de su aplicación.
        </p>
        <p>Por favor, seleccione una de las siguientes opciones para comenzar:</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="mb-3">
                        <i class="fas fa-bolt fa-2x" style="color: #0078d4;"></i>
                    </div>
                    <h5 class="card-title">Instalación Rápida</h5>
                    <p class="card-text text-muted small mb-3 flex-grow-1">
                        Configuración automática con ajustes predeterminados. Ideal para iniciar rápidamente y para entornos de producción estándar.
                    </p>
                    <a href="{{ route('installer.quick') }}" class="btn btn-installer-primary w-100">
                        <i class="fas fa-rocket me-2"></i>Elegir Rápida
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body text-center p-4 d-flex flex-column">
                     <div class="mb-3">
                        <i class="fas fa-sliders-h fa-2x" style="color: #5cb85c;"></i>
                    </div>
                    <h5 class="card-title">Instalación Avanzada</h5>
                    <p class="card-text text-muted small mb-3 flex-grow-1">
                        Control total sobre cada aspecto de la configuración. Recomendado para usuarios experimentados o configuraciones personalizadas.
                    </p>
                    <a href="{{ route('installer.advanced.requirements') }}" class="btn btn-installer w-100" style="background-color: #f0f0f0;">
                        <i class="fas fa-cogs me-2"></i>Elegir Avanzada
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="alert alert-secondary small py-2 px-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Nota Importante:</strong> Antes de iniciar, asegúrese de haber copiado su archivo <code>.env.example</code> a <code>.env</code> si aún no lo ha hecho.
        </div>
    </div>
@endsection

@section('footer-actions')
    {{-- No hay acciones directas en el footer para la página de bienvenida, la acción es elegir una opción. --}}
    {{-- Podríamos poner un botón "Salir" simbólico si fuera una app de escritorio real --}}
    {{-- <button type="button" class="btn btn-installer" disabled>Siguiente &gt;</button> --}}
@endsection