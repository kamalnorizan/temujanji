<nav class="app-header navbar navbar-expand bg-body">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
        </li>
    </ul>

    <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <i class="far fa-user"></i>
                <span class="ms-1">{{ Auth::user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <span class="dropdown-item-text text-muted small">{{ Auth::user()->email }}</span>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-id-card me-2"></i> Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> Log Out
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>

<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link text-decoration-none">
            <i class="fas fa-calendar-check brand-image img-circle elevation-3" style="opacity: .8"></i>
            <span class="brand-text fw-light">{{ config('app.name', 'Temujanji') }}</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <div class="input-group mb-3" data-lte-toggle="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-sidebar" type="button">
                    <i class="fas fa-search fa-fw"></i>
                </button>
            </div>

            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-gauge"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-gear"></i>
                        <p>Profile</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
