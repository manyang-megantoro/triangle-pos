@can('create_sales')
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('sales.create') ? 'c-active' : '' }}" href="{{ route('sales.create') }}">
                <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Create Sale
            </a>
        </li>
    </ul>
@endcan
