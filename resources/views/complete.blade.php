@extends('installer::layout')

@section('title', 'Instalación Completada')

@section('header', '¡Instalación Finalizada!')

@section('description', 'Su aplicación Laravel ha sido configurada exitosamente.')

{{-- Esta vista es la finalización, no muestra el indicador de progreso de pasos --}}
{{-- No se define $stepsData aquí --}}

@section('content')
<div class="text-center">
    <div class="mb-3">
        <i class="fas fa-check-circle fa-4x text-success"></i>
    </div>

    <p class="lead mt-3">
        ¡Felicidades! Su aplicación Laravel ha sido instalada y configurada correctamente.
    </p>
    <p class="mb-4">
        Ya puede comenzar a utilizar su nueva aplicación.
    </p>

    <div class="alert alert-warning mt-4 py-2 px-3 text-start small">
        <h6 class="alert-heading" style="font-size: 0.95rem;"><i class="fas fa-exclamation-triangle me-2"></i>Recordatorios Importantes de Seguridad:</h6>
        <ul class="mb-0 ps-4" style="list-style-type: disc;">
            <li>Si se creó un usuario administrador, cambie la contraseña temporal lo antes posible.</li>
            <li>Para entornos de producción, considere deshabilitar/eliminar este paquete de instalación.</li>
            <li>Si decide mantener el paquete en producción, asegúrese de que la ruta <code>/install</code> no sea accesible públicamente.</li>
        </ul>
    </div>

    <div class="mt-4 text-center">
        <p class="text-muted small">
            Si encuentra algún problema o tiene alguna sugerencia, por favor visite el
            <a href="{{ config('installer.support_url', 'https://github.com/Dansware03/laravelinstaller/issues') }}" target="_blank">
                Repositorio del Proyecto en GitHub
            </a>.
        </p>
    </div>
</div>
@endsection

@section('footer-actions')
    <a href="{{ url('/') }}" class="btn btn-installer-primary">
        <i class="fas fa-home me-1"></i> Ir a la Aplicación
    </a>
@endsection
[end of resources/views/complete.blade.php]
