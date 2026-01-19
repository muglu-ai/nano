<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('delegate.dashboard') }}">
            <span class="ms-1 font-weight-bold">Delegate Panel</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('delegate.dashboard') ? 'active' : '' }}" href="{{ route('delegate.dashboard') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-home text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('delegate.registrations.*') ? 'active' : '' }}" href="{{ route('delegate.registrations.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-list text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Registrations</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('delegate.upgrades.*') ? 'active' : '' }}" href="{{ route('delegate.upgrades.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-arrow-up text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Upgrades</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('delegate.receipts.*') ? 'active' : '' }}" href="{{ route('delegate.receipts.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-receipt text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Receipts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('delegate.notifications.*') ? 'active' : '' }}" href="{{ route('delegate.notifications.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-bell text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Notifications</span>
                    <span id="notification-count-badge" class="badge bg-danger ms-2" style="display: none;">0</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('delegate.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sign-out-alt text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('delegate.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</aside>

<script>
    // Fetch unread notification count
    fetch('{{ route("delegate.notifications.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            const countBadge = document.getElementById('notification-count-badge');
            if(data.count > 0) {
                countBadge.textContent = data.count;
                countBadge.style.display = 'inline';
            }
        });
</script>
