@extends(config('permiso-rol-menu.layout', 'layouts.app'))
@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/permiso-rol-menu/css/menu-tree.css') }}">
@endpush

@section('content')
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
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/permiso-rol-menu/js/permisos-tree.js') }}"></script>
@endpush