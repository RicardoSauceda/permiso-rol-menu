@extends(config('permiso-rol-menu.layout', 'layouts.app'))
@section('title', 'Editar Permiso')
@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/permiso-rol-menu/css/menu-tree.css') }}">
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card p-2">
                <div class="card-header border-0">
                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                    </div>
                    @endif

                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">EDITAR PERMISO</h3>
                            <small class="text-muted">ID: {{ $permiso->id }} | Clave: {{ $permiso->clave_orden }}</small>
                        </div>
                        {{-- <div class="col-4 text-right">
                            <a href="{{ route('permiso-rol-menu.permisos.index') }}" class="btn btn-danger">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div> --}}
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('permiso-rol-menu.permisos.update', $permiso->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="clave_orden" class="form-label">Clave de Orden</label>
                            <input type="text" class="form-control" id="clave_orden" name="clave_orden" readonly value="{{ old('clave_orden', $permiso->clave_orden) }}" placeholder="Ej: 01000000" maxlength="8" required>
                            <small class="form-text text-muted">
                                Formato: 8 dígitos. Los últimos 2 dígitos en '00' para menús principales.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre del Permiso <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                value="{{ old('nombre', $permiso->nombre) }}"
                                placeholder="Nombre descriptivo del permiso" maxlength="255" required>
                        </div>

                        <div class="form-group">
                            <label for="ruta_corta" class="form-label">Ruta Corta<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ruta_corta" name="ruta_corta"
                                value="{{ old('ruta_corta', $permiso->ruta_corta) }}" placeholder="ruta/del/permiso"
                                maxlength="255">
                            <small class="form-text text-muted">
                                Ruta para identificación interna del permiso (opcional).
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                placeholder="Descripción detallada del permiso"
                                maxlength="500">{{ old('descripcion', $permiso->descripcion) }}</textarea>
                        </div>

                        @if(isset($permiso->activo))
                        <div class="form-group">
                            <label class="form-label">Estado Actual</label>
                            <div class="form-control-plaintext">
                                <span class="badge badge-{{ $permiso->activo ? 'success' : 'danger' }}">
                                    {{ $permiso->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                        @endif

                        <div class="form-group text-right">
                            <a href="{{ route('permiso-rol-menu.permisos.arbol', ['claveOrdenPadre' => $permiso->clave_orden]) }}"
                                class="btn btn-danger">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Permiso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Validación de formato de clave de orden
    document.getElementById('clave_orden').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Solo números
        if (value.length > 8) {
            value = value.substr(0, 8);
        }
        e.target.value = value;
    });
});
</script>
@endpush