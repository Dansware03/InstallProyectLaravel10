@extends('installer::layouts.installer')

@section('title', 'Base de Datos')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6">Configuración de Base de Datos</h2>

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('installation.saveDatabase') }}" class="space-y-6">
        @csrf
        <div>
            <label for="database_type" class="block font-medium">Tipo de Base de Datos</label>
            <select name="database_type" id="database_type" class="mt-1 block w-full" onchange="toggleDatabaseFields(this.value)">
                <option value="mysql">MySQL</option>
                <option value="sqlite">SQLite</option>
            </select>
        </div>

        <div id="mysql-fields">
            <div>
                <label for="database_host" class="block font-medium">Host</label>
                <input type="text" name="database_host" id="database_host" class="mt-1 block w-full" required>
            </div>
            <div>
                <label for="database_port" class="block font-medium">Puerto</label>
                <input type="text" name="database_port" id="database_port" class="mt-1 block w-full" required>
            </div>
            <div>
                <label for="database_name" class="block font-medium">Nombre</label>
                <input type="text" name="database_name" id="database_name" class="mt-1 block w-full" required>
            </div>
            <div>
                <label for="database_user" class="block font-medium">Usuario</label>
                <input type="text" name="database_user" id="database_user" class="mt-1 block w-full" required>
            </div>
            <div>
                <label for="database_password" class="block font-medium">Contraseña</label>
                <input type="password" name="database_password" id="database_password" class="mt-1 block w-full">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded hover:bg-primary-700">Guardar y Continuar</button>
        </div>
    </form>
</div>

<script>
function toggleDatabaseFields(type) {
    const mysqlFields = document.getElementById('mysql-fields');
    mysqlFields.style.display = type === 'mysql' ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', () => {
    toggleDatabaseFields(document.getElementById('database_type').value);
});
</script>
@endsection
