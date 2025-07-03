class PermisosTree {
    constructor() {
        this.init();
    }

    init() {
        this.bindPermissionStatusToggle();
        this.bindFormCollapse();
    }

    bindPermissionStatusToggle() {
        document.querySelectorAll('.permission-status').forEach(statusElement => {
            statusElement.addEventListener('click', (e) => this.handlePermissionStatusToggle(e));
        });
    }

    bindFormCollapse() {
        $(document).on('show.bs.collapse', '#add-permiso-form', function () {
            // Cerrar otros formularios si están abiertos
            $('.collapse.show').not(this).collapse('hide');
        });
    }

    handlePermissionStatusToggle(e) {
        const statusElement = e.target;
        const permisoId = statusElement.getAttribute('data-id-permiso');
        const currentStatus = statusElement.classList.contains('badge-success') ? 'Activo' : 'Inactivo';
        const newStatus = currentStatus === 'Activo' ? 'Inactivo' : 'Activo';

        this.updatePermissionStatus(permisoId, newStatus, statusElement);
    }

    updatePermissionStatus(permisoId, newStatus, statusElement) {
        $.ajax({
            url: `/permiso-rol-menu/permisos/${permisoId}/status-update`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => this.handlePermissionStatusUpdateSuccess(response, statusElement),
            error: (xhr) => {
                console.error('Error:', xhr);
                alert('Error al cambiar el estado del permiso.');
            }
        });
    }

    handlePermissionStatusUpdateSuccess(response, statusElement) {
        if (response.success) {
            // Actualizar el elemento visual
            statusElement.classList.toggle('badge-success');
            statusElement.classList.toggle('badge-danger');
            statusElement.textContent = response.newStatus;
        } else {
            alert('Error al cambiar el estado del permiso: ' + response.message);
        }
    }
}

// Función global para eliminar permisos
function deletePermission(permisoId, permisoName) {
    if (confirm(`¿Estás seguro de que deseas eliminar el permiso "${permisoName}"?\n\nEsta acción no se puede deshacer.`)) {
        // Crear un formulario temporal para enviar la petición DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/permiso-rol-menu/permisos/${permisoId}`;
        
        // Agregar token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = $('meta[name="csrf-token"]').attr('content');
        form.appendChild(csrfToken);
        
        // Agregar método DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Agregar al DOM y enviar
        document.body.appendChild(form);
        form.submit();
    }
}

// Inicializar cuando el DOM esté listo
$(document).ready(() => new PermisosTree());
