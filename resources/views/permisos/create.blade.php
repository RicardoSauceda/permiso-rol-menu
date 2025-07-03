@extends(config('permiso-rol-menu.layout', 'layouts.app'))
@section('title', 'Crear Permiso')
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
                            <h3 class="mb-0">CREAR NUEVO PERMISO</h3>
                            @if(isset($menuPadre))
                            <small class="text-muted">Menú padre: {{ $menuPadre->nombre }} ({{ $claveOrdenPadre }})</small>
                            @endif
                        </div>
                        {{-- <div class="col-4 text-right">
                            <a href="{{ route('permiso-rol-menu.permisos.arbol', $claveOrdenPadre) }}" class="btn btn-danger">
                                <i class="fas fa-arrow-left"></i> Volver al Menú Padre
                            </a>
                        </div> --}}
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('permiso-rol-menu.permisos.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="clave_orden_padre" value="{{ $claveOrdenPadre }}">

                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre del Permiso <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre"
                                name="nombre" value="{{ old('nombre') }}" placeholder="Nombre descriptivo del permiso"
                                maxlength="255" required>
                        </div>

                        <div class="form-group">
                            <label for="ruta_corta" class="form-label">Ruta Corta<span class="text-danger">*</span></label>
                            <input type="text" class="form-control"
                                id="ruta_corta" name="ruta_corta" value="{{ old('ruta_corta') }}"
                                placeholder="ruta/del/permiso" maxlength="255">
                            <small class="form-text text-muted">
                                Ruta para identificación interna del permiso (opcional).
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion"
                                name="descripcion" rows="3" placeholder="Descripción detallada del permiso"
                                maxlength="500">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="form-group text-right">
                            <a href="{{ route('permiso-rol-menu.permisos.arbol', $claveOrdenPadre) }}" class="btn btn-danger">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Permiso
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
@endpush