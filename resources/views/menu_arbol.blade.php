@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/permiso-rol-menu/css/menu-tree.css') }}">
@endpush

<div class="row" id="menu-tree">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif
                @error('menu')
                <div class="alert alert-error">
                    <p>{{ $message }}</p>
                </div>
                @enderror
                <div class="row align-items-center">
                    <div class="col-8">
                        <h3 class="mb-0">MENUS</h3>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tree-menu">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Árbol de Menús</h4>
                        <button class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#add-root-menu">
                            <i class="fas fa-plus"></i> Añadir Menú Principal
                        </button>
                    </div>

                    {{-- Formulario para agregar menú de primer nivel --}}
                    <div id="add-root-menu" class="collapse mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Nuevo Menú Principal</h6>
                                <form method="POST" action="{{ route('permiso-rol-menu.tree.menus.store') }}">
                                    @csrf
                                    <input type="hidden" name="menu" value="1">
                                    <input type="hidden" name="activo" value="1">

                                    @if ($errors->any())
                                    <div class="alert alert-danger alert-sm">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                            <li><small>{{ $error }}</small></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><small>Nombre *</small></label>
                                                <input type="text" name="permisoName"
                                                    class="form-control form-control-sm @error('permisoName') is-invalid @enderror"
                                                    value="{{ old('permisoName') }}" required>
                                                @error('permisoName')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><small>Ruta Corta *</small></label>
                                                <input type="text" name="rutaCorta"
                                                    class="form-control form-control-sm @error('rutaCorta') is-invalid @enderror"
                                                    value="{{ old('rutaCorta') }}" required>
                                                @error('rutaCorta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><small>Descripción</small></label>
                                        <textarea name="permisoDescripcion"
                                            class="form-control form-control-sm @error('permisoDescripcion') is-invalid @enderror"
                                            rows="2">{{ old('permisoDescripcion') }}</textarea>
                                        @error('permisoDescripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-2">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-save"></i> Crear Menú Principal
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary ml-1"
                                            data-toggle="collapse" data-target="#add-root-menu">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="tree-container">
                        <ul class="menu-tree">
                            @if(isset($menuTree) && count($menuTree) > 0)
                            @foreach($menuTree as $menuItems)
                            @include('permiso-rol-menu::menu_items', ['menu' => $menuItems, 'level' => 0])
                            @endforeach
                            @else
                            <li class="text-muted">
                                <i class="fas fa-info-circle"></i> No hay menús disponibles.
                                <a href="#" data-toggle="collapse" data-target="#add-root-menu">Haz clic aquí para crear
                                    el primer menú</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/permiso-rol-menu/js/menu-tree.js') }}"></script>
@endpush