<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
                <img src="{{ asset('assets/img/ktb.png') }}" alt="navbar brand" class="navbar-brand" height="65" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Menu</h4>
                </li>

                @if (in_array(Auth::user()->role, ['bar', 'manager']))
                    <li class="nav-item {{ request()->routeIs('bar.detail') ? 'active' : '' }}">
                        <a href="{{ route('bar.detail') }}">
                            <i class="fas fa-file"></i>
                            <p>Bar</p>
                        </a>
                    </li>
                @endif

                @if (in_array(Auth::user()->role, ['kitchen', 'manager']))
                    <li class="nav-item {{ request()->routeIs('kitchen.detail') ? 'active' : '' }}">
                        <a href="{{ route('kitchen.detail') }}">
                            <i class="fas fa-file"></i>
                            <p>Kitchen</p>
                        </a>
                    </li>
                @endif
                @if (in_array(Auth::user()->role, ['roti', 'manager']))
                    <li class="nav-item {{ request()->routeIs('roti.detail') ? 'active' : '' }}">
                        <a href="{{ route('roti.detail') }}">
                            <i class="fas fa-file"></i>
                            <p>Roti</p>
                        </a>
                    </li>
                @endif



                @if (Auth::user()->role == 'manager')
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Settings</h4>
                    </li>
                    <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}">
                            <i class="fas fa-user"></i>
                            <p>Management User</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('bar.index') ? 'active' : '' }}">
                        <a href="{{ route('bar.index') }}">
                            <i class="fas fa-chart-bar"></i>
                            <p>Data Bar</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('kitchen.index') ? 'active' : '' }}">
                        <a href="{{ route('kitchen.index') }}">
                            <i class="fas fa-chart-bar"></i>
                            <p>Data Kitchen</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('roti.index') ? 'active' : '' }}">
                        <a href="{{ route('roti.index') }}">
                            <i class="fas fa-chart-bar"></i>
                            <p>Data Roti</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
                        <a href="{{ route('laporan.index') }}">
                            <i class="fas fa-book"></i>
                            <p>Laporan</p>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
