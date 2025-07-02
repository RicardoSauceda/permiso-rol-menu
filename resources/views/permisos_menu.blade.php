@extends(config('permiso-rol-menu.layout', 'layouts.app'))
@section('title', 'Permisos y Menús')
@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/permiso-rol-menu/css/menu-tree.css') }}">
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="row" id="permisos-tree">
        <div class="col-xl-12">
            <div class="card p-2">
                <div class="card-header border-0">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    <div class="row align-items-center">
                        <div class="col-4">
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
                        <div class="col-4 text-right">
                            <a href="{{ route('permiso-rol-menu.permisos.create', $claveOrdenPadre) }}"
                                class="btn btn-success">
                                <i class="fas fa-plus"></i> Crear Permiso
                            </a>
                        </div>
                    </div>
                </div>
                <d class="card-body">
                    @if(!$permisos->isEmpty())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Permiso</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permisos as $permiso)
                                <tr>
                                    <td>{{ $permiso->nombre }}</td>
                                    <td>{{ $permiso->descripcion }}</td>
                                    <td>
                                        <a href="{{ route('permiso-rol-menu.permisos.edit', $permiso->id) }}"
                                            class="btn btn-primary btn-sm">Editar</a>
                                        <form action="{{ route('permiso-rol-menu.permisos.destroy', $permiso->id) }}" method="POST" style="display:inline;"
                                            onsubmit="return confirm('¿Está seguro de eliminar este permiso?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-light text-center m-0 w-100 p-2">
                        <div class="d-flex align-items-center justify-content-center" style="height: 30px;">
                            <p class="m-0">No hay permisos registrados en este menú.</p>
                        </div>
                    </div>
                    @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/permiso-rol-menu/js/permisos-tree.js') }}"></script>
@endpush