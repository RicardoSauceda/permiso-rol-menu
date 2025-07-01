<li class="tree-level-permission mb-2">
    <div class="d-flex justify-content-between align-items-center p-2 border rounded">
        <div class="d-flex align-items-center">
            <div class="permission-icon mr-3">
                <i class="fas fa-key text-muted"></i>
            </div>
            <div>
                <strong class="permission-name">{{ $permiso['nombre'] }}</strong>
                <small class="text-muted ml-2">({{ $permiso['ruta_corta'] }})</small>
                <small class="text-muted ml-2">({{ $permiso['clave_orden'] }})</small>
                @if($permiso['descripcion'])
                <div><small class="text-muted">{{ $permiso['descripcion'] }}</small></div>
                @endif
            </div>
        </div>
        
        <div class="d-flex align-items-center">
            <span id="permission-status-{{ $permiso['id'] }}" 
                  style="cursor: pointer;" 
                  class="permission-status badge badge-{{ $permiso['activo'] ? 'success' : 'danger' }} mr-2" 
                  data-id-permiso="{{ $permiso['id'] }}">
                {{ $permiso['activo'] ? 'Activo' : 'Inactivo' }}
            </span>
            
            <div class="dropdown">
                <button class="btn btn-sm btn-link text-muted" type="button" id="dropdownMenuButton{{ $permiso['id'] }}" data-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <button class="dropdown-item" data-toggle="modal" data-target="#editPermissionModal{{ $permiso['id'] }}">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <div class="dropdown-divider"></div>
                    <button class="dropdown-item text-danger" onclick="deletePermission({{ $permiso['id'] }}, '{{ $permiso['nombre'] }}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</li>

<!-- Modal para editar permiso -->
<div class="modal fade" id="editPermissionModal{{ $permiso['id'] }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Permiso</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('permiso-rol-menu.permisos.update', $permiso['id']) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre del Permiso</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $permiso['nombre'] }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Ruta Corta</label>
                        <input type="text" name="ruta_corta" class="form-control" value="{{ $permiso['ruta_corta'] }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ $permiso['descripcion'] }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Clave de Orden</label>
                        <input type="text" name="clave_orden" class="form-control" value="{{ $permiso['clave_orden'] }}" readonly>
                        <small class="form-text text-muted">La clave de orden no se puede modificar</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
