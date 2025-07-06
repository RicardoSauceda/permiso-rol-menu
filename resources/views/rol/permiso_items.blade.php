<li class="tree-level-{{$level}} mb-2">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            @if(isset($menu['submenu']) && count($menu['submenu']) > 0)
            <button class="btn btn-sm btn-link p-0 mr-2" data-toggle="collapse"
                data-target="#submenu-{{ $menu['clave_orden'] }}">
                <i class="fas fa-chevron-right"></i>
            </button>
            @else
            <span class="d-inline-block mr-3"></span>
            @endif
            <div data-toggle="collapse" data-target="#submenu-{{ $menu['clave_orden'] }}" style="cursor: pointer;">
                <strong class="menu-name">{{ $menu['nombre'] }}</strong>
                <small class="text-muted">({{ $menu['ruta_corta'] }})</small>
                <small class="text-muted mr-3">({{ $menu['clave_orden'] }})</small>
                {{-- <div><small>{{ $menu['description'] }}</small></div> --}}
            </div>

        </div>
        <div class="d-flex align-items-center">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input permiso-checkbox" id="permiso-{{ $menu['id'] }}"
                    name="menu[{{ $menu['id'] }}]" value="{{ $menu['id'] }}" {{ $rol->menus->contains($menu['id']) ?
                'checked' : '' }}
                data-permiso-id="{{ $menu['id'] }}"
                data-clave-orden="{{ $menu['clave_orden'] }}">
                <label class="custom-control-label" for="permiso-{{ $menu['id'] }}"></label>
            </div>
        </div>
    </div>
    @if(isset($menu['submenu']) && count($menu['submenu']) > 0)
    <ul id="submenu-{{ $menu['clave_orden'] }}" class="collapse">
        @foreach($menu['submenu'] as $child)
        @include('permiso-rol-menu::rol.permiso_items', ['menu' => $child, 'level' => $level + 1])
        @endforeach
    </ul>
    @endif
</li>