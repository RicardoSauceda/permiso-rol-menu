@extends(config('permiso-rol-menu.layout', 'layouts.app'))
@section('title', 'Permisos y Menús')
@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/permiso-rol-menu/css/menu-tree.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/permiso-rol-menu/css/permisos-tree.css') }}">
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="row" id="menu-tree">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="alert-container p-2"></div>
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Menu para rol: {{ $rol->nombre }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="toggle-all-permisos">
                                <i class="fas fa-check-square"></i> Seleccionar Todo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tree-menu">
                        <h4>Árbol de Menús</h4>
                        <div class="tree-container">
                            <ul class="menu-tree">
                                @if(isset($menuTree) && count($menuTree) > 0)
                                    @foreach($menuTree as $menuItems)
                                        @include('permiso-rol-menu::rol.permiso_items', ['menu' => $menuItems, 'level' => 0])
                                    @endforeach
                                @else
                                <li>No hay menús disponibles</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/permiso-rol-menu/js/permisos-tree.js') }}"></script>
@endpush