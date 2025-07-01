@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/permiso-rol-menu/css/menu-tree.css') }}">
@endpush

<div class="row" id="permisos-tree">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif
                <div class="row align-items-center">
                    <div class="col-8">
                        <h3 class="mb-0">PERMISOS</h3>
                    </div>
                    <div class="col-4 text-right">
                        @if(isset($menuPadre))
                        <div class="d-flex align-items-center justify-content-end">
                            <small class="text-muted mr-2">Menú padre:</small>
                            <span class="badge badge-info">{{ $menuPadre['nombre'] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tree-menu">
                    <h4>Permisos del Menú</h4>
                    @if(isset($menuPadre))
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $menuPadre['nombre'] }}</strong>
                                <small class="text-muted ml-2">({{ $menuPadre['ruta_corta'] }})</small>
                                <small class="text-muted ml-2">({{ $menuPadre['clave_orden'] }})</small>
                            </div>
                            <div>
                                <span class="badge badge-{{ $menuPadre['activo'] ? 'success' : 'danger' }}">
                                    {{ $menuPadre['activo'] ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                        @if($menuPadre['descripcion'])
                        <div class="mt-2">
                            <small class="text-muted">{{ $menuPadre['descripcion'] }}</small>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    <div class="tree-container">
                        <ul class="menu-tree">
                            @if(isset($permisosTree) && count($permisosTree) > 0)
                                @foreach($permisosTree as $permiso)
                                    @include('permiso-rol-menu::permiso_items', ['permiso' => $permiso, 'menuPadre' => $menuPadre ?? null])
                                @endforeach
                            @else
                            <li class="text-muted">No hay permisos específicos para este menú</li>
                            @endif
                            
                            <!-- Formulario para agregar nuevo permiso -->
                            @if(isset($menuPadre))
                            <li class="mt-3">
                                <button class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#add-permiso-form">
                                    <i class="fas fa-plus"></i>
                                    Agregar Permiso
                                </button>
                                <div id="add-permiso-form" class="collapse mt-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <form method="POST" action="{{ route('permiso-rol-menu.permisos.store-permiso') }}">
                                                @csrf
                                                <input type="hidden" name="menu" value="0">
                                                <input type="hidden" name="activo" value="1">
                                                <input type="hidden" name="clave_orden_padre" value="{{ $menuPadre['clave_orden'] }}">
                                                
                                                <div class="form-group">
                                                    <label for="permisoName">Nombre del Permiso</label>
                                                    <input type="text" name="permisoName" id="permisoName" class="form-control" required 
                                                           placeholder="ej: Crear, Editar, Eliminar, Ver">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="rutaCorta">Ruta Corta</label>
                                                    <input type="text" name="rutaCorta" id="rutaCorta" class="form-control" required 
                                                           placeholder="ej: crear, editar, eliminar, ver">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="permisoDescripcion">Descripción</label>
                                                    <textarea name="permisoDescripcion" id="permisoDescripcion" class="form-control" rows="2" 
                                                              placeholder="Descripción detallada del permiso"></textarea>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between">
                                                    <button type="button" class="btn btn-secondary" data-toggle="collapse" data-target="#add-permiso-form">
                                                        Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save"></i>
                                                        Guardar Permiso
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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
<script src="{{ asset('vendor/permiso-rol-menu/js/permisos-tree.js') }}"></script>
@endpush
