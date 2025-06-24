@extends('installer::layout')

@section('title', 'Bienvenido al Asistente de Instalación')

@section('header', 'Bienvenido')

@section('description', 'Seleccione el tipo de instalación que desea realizar')

@section('content')
<div class="text-center">
    <div class="mb-4">
        <i class="fas fa-rocket" style="font-size: 4rem; color: #FF512F;"></i>
    </div>
    
    <h3 class="mb-4">¡Bienvenido al Asistente de Instalación!</h3>
    
    <p class="lead mb-5">
        Este asistente le ayudará a configurar su aplicación Laravel de manera rápida y segura.
        Seleccione una de las siguientes opciones para comenzar:
    </p>
    
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-bolt text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="card-title">Instalación Rápida</h4>
                    <p class="card-text">
                        Configuración automática con ajustes predeterminados optimizados para producción.
                        Solo necesita proporcionar los datos de la base de datos.
                    </p>
                    <ul class="list-unstyled text-start mb-4">
                        <li><i class="fas fa-check text-success me-2"></i>Verificación automática de requisitos</li>
                        <li><i class="fas fa-check text-success me-2"></i>Configuración de seguridad</li>
                        <li><i class="fas fa-check text-success me-2"></i>Optimizaciones de producción</li>
                        <li><i class="fas fa-check text-success me-2"></i>Usuario administrador automático</li>
                    </ul>
                    <a href="{{ route('installer.quick') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-rocket me-2"></i>Instalación Rápida
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-cogs text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="card-title">Instalación Avanzada</h4>
                    <p class="card-text">
                        Configuración paso a paso con control total sobre cada aspecto de la instalación.
                        Recomendado para usuarios experimentados.
                    </p>
                    <ul class="list-unstyled text-start mb-4">
                        <li><i class="fas fa-check text-primary me-2"></i>Control total de la configuración</li>
                        <li><i class="fas fa-check text-primary me-2"></i>Verificación manual de requisitos</li>
                        <li><i class="fas fa-check text-primary me-2"></i>Configuración de correo</li>
                        <li><i class="fas fa-check text-primary me-2"></i>Entorno desarrollo/producción</li>
                    </ul>
                    <a href="{{ route('installer.advanced.requirements') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-cogs me-2"></i>Instalación Avanzada
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-5">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Importante:</strong> Asegúrese de haber copiado el archivo <code>.env.example</code> a <code>.env</code> 
            antes de comenzar la instalación.
        </div>
    </div>
</div>
@endsection