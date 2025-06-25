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

    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Recordatorio de Seguridad:</strong>
        <ul class="mt-2 mb-0 text-start">
            <li>Si creó un usuario administrador durante la instalación, cambie la contraseña temporal lo antes posible.</li>
            <li>Por seguridad, considere deshabilitar o eliminar el paquete de instalación en entornos de producción una vez que todo esté configurado.</li>
            <li>Asegúrese de que la ruta <code>/install</code> no sea accesible públicamente en producción si decide mantener el paquete. El middleware de instalación debería prevenir esto, pero una capa extra de seguridad (ej. reglas de servidor web) es recomendable.</li>
        </ul>
    </div>

    <div class="mt-4">
        <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-home me-2"></i>Ir a la Página Principal
        </a>
    </div>

    <div class="mt-5">
        <p class="text-muted">
            Si encuentra algún problema o tiene alguna sugerencia, por favor visite el
            <a href="{{ config('installer.support_url', 'https://github.com/Dansware03/laravelinstaller/issues') }}" target="_blank">repositorio del proyecto</a>.
        </p>
    </div>
</div>
@endsection
[end of resources/views/complete.blade.php]
