@extends('installer::layout')

@section('title', 'Instalación Completada')

@section('header', '¡Instalación Finalizada!')

@section('description', 'Su aplicación Laravel ha sido configurada exitosamente.')

@section('content')
<div class="text-center">
    <div class="mb-4">
        <i class="fas fa-check-circle fa-4x text-success"></i>
    </div>

    <h3 class="mb-3">¡Felicidades!</h3>

    <p class="lead mb-4">
        Su aplicación Laravel ha sido instalada y configurada correctamente.
        Ya puede comenzar a utilizarla.
    </p>

    <div class="alert alert-warning mt-4 py-3 px-4">
        <div class="d-flex">
            <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1"></i>
            <div>
                <h5 class="alert-heading h6">Recordatorios de Seguridad Importantes:</h5>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-1"><i class="fas fa-key me-1"></i>Cambie la contraseña del administrador si se creó una.</li>
                    <li class="mb-1"><i class="fas fa-shield-alt me-1"></i>Considere deshabilitar/eliminar este paquete en producción.</li>
                    <li><i class="fas fa-lock me-1"></i>Asegure la ruta <code>/install</code> si mantiene el paquete.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3">
        <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-home me-2"></i>Ir a la Página Principal
        </a>
    </div>

    <div class="mt-5 pt-2">
        <p class="text-muted small">
            Si encuentra algún problema o tiene alguna sugerencia, por favor visite el<br>
            <a href="{{ config('installer.support_url', 'https://github.com/Dansware03/laravelinstaller/issues') }}" target="_blank" class="text-decoration-none">
                <i class="fab fa-github me-1"></i>Repositorio del Proyecto
            </a>
        </p>
    </div>
</div>
@endsection
[end of resources/views/complete.blade.php]
