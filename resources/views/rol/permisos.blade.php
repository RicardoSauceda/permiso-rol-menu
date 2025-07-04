@extends(config('permiso-rol-menu.layout', 'layouts.app'))
@section('title', 'Permisos y Menús')
@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/permiso-rol-menu/css/menu-tree.css') }}">
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="row" id="menu-tree">
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
                            <h3 class="mb-0">Menu</h3>
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
                                        @include('permiso-rol-menu::permiso_items', ['menu' => $menuItems, 'level' => 0])
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