@can('migration')
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('migrator.index') ? 'c-active' : '' }}" href="{{ route('migrator.index') }}">
                <i class="c-sidebar-nav-icon bi bi-box-arrow-in-down" style="line-height: 1;"></i>{{ __('Migrator' ) }}
            </a>
        </li>
    </ul>
@endcan
