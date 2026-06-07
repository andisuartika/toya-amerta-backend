<!-- Topbar Start -->
<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button class="button-toggle-menu nav-link">
                        <i data-feather="menu" class="noti-icon"></i>
                    </button>
                </li>
                <li class="d-none d-lg-block">
                    <span class="text-muted fs-13">
                        <i data-feather="droplet" class="me-1 text-primary" style="width:14px;height:14px;"></i>
                        Sistem PDAM Desa Toya Amerta
                    </span>
                </li>
            </ul>

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">

                <!-- Fullscreen -->
                <li class="d-none d-sm-flex">
                    <button type="button" class="btn nav-link" data-toggle="fullscreen">
                        <iconify-icon icon="solar:minimize-square-outline" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </li>

                <!-- Dark Mode -->
                <li class="d-none d-sm-flex">
                    <button type="button" class="btn nav-link" id="light-dark-mode">
                        <iconify-icon icon="solar:moon-outline" class="fs-24 align-middle dark-mode"></iconify-icon>
                        <iconify-icon icon="solar:sun-2-outline" class="fs-24 align-middle light-mode"></iconify-icon>
                    </button>
                </li>

                <!-- User Profile -->
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#"
                       role="button" aria-haspopup="false" aria-expanded="false">
                        <div class="avatar-sm d-inline-block me-1">
                            <span class="avatar-title bg-primary rounded-circle fs-14 fw-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <span class="d-none d-md-inline-block fs-13 fw-medium">{{ Auth::user()->name }}</span>
                        <i data-feather="chevron-down" class="ms-1" style="width:14px;height:14px;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">{{ Auth::user()->name }}</h6>
                            <small class="text-muted text-capitalize">{{ Auth::user()->role }}</small>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}" id="topbar-logout-form">
                            @csrf
                        </form>
                        <a href="#" class="dropdown-item notify-item rounded-2"
                           onclick="event.preventDefault(); document.getElementById('topbar-logout-form').submit();">
                            <iconify-icon icon="solar:login-3-outline" class="fs-18 align-middle me-1"></iconify-icon>
                            <span>Keluar</span>
                        </a>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>
<!-- end Topbar -->
