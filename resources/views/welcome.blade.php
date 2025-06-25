@extends('installer::layout')

@section('title', 'Bienvenido al Asistente de Instalación')

@section('header', 'Bienvenido')

@section('description', 'Seleccione el tipo de instalación que desea realizar')

@section('content')
<div class="text-center pt-2">
    <div class="mb-4">
        <i class="fas fa-cogs" style="font-size: 4rem; color: #FF512F;"></i>
    </div>
    
    {{-- Título h3 eliminado, ya que @yield('header') lo maneja ahora --}}
    
    <p class="lead mb-5">
        Este asistente le ayudará a configurar su aplicación Laravel de manera rápida y segura.<br>
        Seleccione una de las siguientes opciones para comenzar:
    </p>
    
    <div class="row g-4 justify-content-center">
        <div class="col-lg-6 d-flex align-items-stretch">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-lg-5 p-4 d-flex flex-column">
                    <div class="mb-4">
                        <i class="fas fa-bolt text-success" style="font-size: 3.5rem;"></i>
                    </div>
                    <h4 class="card-title h5">Instalación Rápida</h4>
                    <p class="card-text text-muted small mb-4">
                        Configuración automática con ajustes predeterminados. Ideal para iniciar rápidamente.
                    </p>
                    <ul class="list-unstyled text-start mb-4 small">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Verificación de requisitos</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Configuración de seguridad</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Optimizaciones de producción</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Usuario administrador</li>
                    </ul>
                    <a href="{{ route('installer.quick') }}" class="btn btn-success btn-lg mt-auto">
                        <i class="fas fa-rocket me-2"></i>Elegir Rápida
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 d-flex align-items-stretch">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-lg-5 p-4 d-flex flex-column">
                    <div class="mb-4">
                        <i class="fas fa-cogs text-primary" style="font-size: 3.5rem;"></i>
                    </div>
                    <h4 class="card-title h5">Instalación Avanzada</h4>
                    <p class="card-text text-muted small mb-4">
                        Control total sobre cada aspecto de la configuración. Para usuarios experimentados.
                    </p>
                    <ul class="list-unstyled text-start mb-4 small">
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Control total</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Verificación manual</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Configuración de correo</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Entorno y optimizaciones</li>
                    </ul>
                    <a href="{{ route('installer.advanced.requirements') }}" class="btn btn-primary btn-lg mt-auto">
                        <i class="fas fa-cogs me-2"></i>Elegir Avanzada
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-5 pt-3">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Importante:</strong> Asegúrese de haber copiado el archivo <code>.env.example</code> a <code>.env</code> 
            antes de comenzar la instalación.
        </div>
    </div>
</div>
@endsection