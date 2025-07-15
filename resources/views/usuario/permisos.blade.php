@extends(config('permiso-rol-menu.layout', 'layouts.app'))

@section('title', 'ASIGNAR PERMISO USUARIO | Sivyc Icatech')

@section('content')
<div class="container-fluid mt--6">
    <div class="row" id="menu-tree">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="alert-container p-2">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Menu para: {{ $usuario->nombre }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button type="submit" form="permisos-form" class="btn btn-sm btn-primary">
                                <i class="fas fa-save"></i> Guardar Permisos
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tree-menu">
                        <h4>Árbol de Menús</h4>
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Los permisos marcados con <span class="text-info"> <i class="fas fa-users"></i> </span> están asignados a través de roles y no pueden ser modificados directamente.
                            </small>
                        </div>
                        <form id="permisos-form" method="POST" action="{{ route('permiso-rol-menu.usuarios.permisos.guardar', $usuario->id) }}">
                            @csrf
                            <div class="tree-container">
                                <ul class="menu-tree">
                                    @if(isset($menuTree) && count($menuTree) > 0)
                                        @foreach($menuTree as $menuItems)
                                            @include('permiso-rol-menu::usuario.permiso_items', ['menu' => $menuItems, 'level' => 0])
                                        @endforeach
                                    @else
                                    <li>No hay menús disponibles</li>
                                    @endif
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Manejo de expansión/colapso de submenús
    $('[data-toggle="collapse"]').on('click', function() {
        const target = $(this).find('i.fas');
        const targetId = $(this).data('target');
        
        setTimeout(() => {
            const isExpanded = $(targetId).hasClass('show');
            if (isExpanded) {
                target.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            } else {
                target.removeClass('fa-chevron-down').addClass('fa-chevron-right');
            }
        }, 350);
    });

    // Prevenir clicks en checkboxes deshabilitados
    $('.permiso-checkbox:disabled').on('click', function(e) {
        e.preventDefault();
        return false;
    });

    // Manejo del envío del formulario
    $('#permisos-form').on('submit', function(e) {
        e.preventDefault();
        
        const enabledCheckboxes = $('.permiso-checkbox:not(:disabled)');
        const checkedCount = enabledCheckboxes.filter(':checked').length;
        
        // Mostrar confirmación
        if (confirm(`¿Confirmar asignación de permisos directos al usuario? Se asignarán ${checkedCount} permisos directos.`)) {
            // Crear un formulario temporal para enviar solo los checkboxes habilitados y marcados
            const form = $(this);
            const formData = new FormData();
            
            // Agregar CSRF token
            formData.append('_token', $('input[name="_token"]').val());
            
            // Agregar solo los permisos habilitados y marcados
            enabledCheckboxes.filter(':checked').each(function() {
                formData.append('menu[' + $(this).val() + ']', $(this).val());
            });
            
            // Enviar vía AJAX para mejor control
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    let message = 'Error al guardar los permisos';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        }
    });
});
</script>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/permiso-rol-menu/css/permiso-user.css') }}?v={{ time() }}">
@endpush