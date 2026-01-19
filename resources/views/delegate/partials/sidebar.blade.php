<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('delegate.dashboard') }}">
            <div class="d-flex align-items-center">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-user-circle text-white text-sm"></i>
                </div>
                <div>
                    <span class="ms-1 font-weight-bold text-dark">Delegate</span>
                    <p class="mb-0 text-xs text-muted">Panel</p>
                </div>
            </div>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('delegate.dashboard') ? 'active' : '' }}" href="{{ route('delegate.dashboard') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-home text-white text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('delegate.registrations.*') ? 'active' : '' }}" href="{{ route('delegate.registrations.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-info text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-list text-white text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Registrations</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('delegate.upgrades.*') ? 'active' : '' }}" href="{{ route('delegate.upgrades.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-success text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-arrow-up text-white text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Upgrades</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('delegate.receipts.*') ? 'active' : '' }}" href="{{ route('delegate.receipts.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-warning text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-receipt text-white text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Receipts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('delegate.notifications.*') ? 'active' : '' }}" href="{{ route('delegate.notifications.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-bell text-white text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Notifications</span>
                    <span id="notification-count-badge" class="badge bg-danger ms-2 rounded-pill" style="display: none; font-size: 0.65rem; padding: 0.25rem 0.5rem;">0</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <hr class="horizontal dark">
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="{{ route('delegate.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sign-out-alt text-white text-sm opacity-10"></i>
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

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .bg-gradient-danger {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    .nav-link.active {
        background-color: rgba(102, 126, 234, 0.1);
        border-left: 3px solid #667eea;
    }
    .nav-link:hover:not(.active) {
        background-color: rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }
</style>

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
        })
        .catch(error => console.error('Error fetching notification count:', error));
</script>
