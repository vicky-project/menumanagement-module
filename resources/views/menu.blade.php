@use('Modules\MenuManagement\Services\MenuService')

@php
  $menuService = app(MenuService::class);
  $menus = $menuService->getMenuForUser(auth()->user());
@endphp

@foreach($menus as $menu)
  @if(isset($menu["type"]) && $menu["type"] === "group" && isset($menu["children"]))
    <!-- Menu dengan submenu -->
    <li class="nav-group">
      <a class="nav-link nav-group-toggle" href="#">
        @isset($menu["icon"])
          <svg class="nav-icon">
            <use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-'. $menu['icon']) }}"></use>
          </svg>
        @endisset
        <span>{{ $menu["name"] }}</span>
      </a>
      <ul class="nav-group-items compact">
        @foreach($menu["children"] as $child)
          <li class="nav-item">
            <a href="{{ isset($child['route']) ? route($child['route']) : ($child['url'] ?? '#') }}" class="nav-link"
              @isset($child["target"])
                target="{{$child['target']}}"
              @endisset
              >
              @isset($child["icon"])
                <svg class="nav-icon">
                  <use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-'. $child['icon']) }}"></use>
                </svg>
              @endisset
              <span>{{ $child["name"] }}</span>
            </a>
          </li>
        @endforeach
      </ul>
    </li>
  @elseif(isset($menu["type"]) && $menu["type"] === "separator")
    <li class="nav-divider"></li>
  @elseif(isset($menu["type"]) && $menu["type"] === "title")
    <li class="nav-title">{{ $menu["name"] }}</li>
  @else
    <!-- Menu tunggal -->
    <li class="nav-item">
      <a href="{{ isset($menu['route']) ? route($menu['route']) : ($menu['url'] ?? '#') }}" class="nav-link"
        @isset($menu['target'])
          target="{{ $menu['target'] }}"
        @endisset
        >
        @isset($menu["icon"])
          <svg class="nav-icon">
            <use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-'.$menu['icon']) }}"></use>
          </svg>
        @endisset
        <span>{{ $menu["name"] }}</span>
      </a>
    </li>
  @endif
@endforeach

@if(count($menus) === 0)
<li class="nav-item">
  <a href="#" class="nav-link text-muted">
    <svg class="nav-icon"><use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-info-circle') }}"/></use>
    <span>No menu available</span>
  </a>
</li>
@endif