<!-- Left Sidebar Start -->
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">

            <!-- Logo -->
            <div class="logo-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/logo-light.png" alt="" height="24">
                    </span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/logo-dark.png" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul id="side-menu">

                <!-- ── DASHBOARD ── -->
                <li class="menu-title">Menu Utama</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                        </span>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- ── MASTER DATA ── -->
                @role('admin')
                <li class="menu-title mt-2">Master Data</li>

                <li>
                    <a href="{{ route('admin.zones.index') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:map-point-bold-duotone"></iconify-icon>
                        </span>
                        <span>Zona Wilayah</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.tariff-rates.index') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:tag-price-bold-duotone"></iconify-icon>
                        </span>
                        <span>Tarif Air</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.customers.index') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon>
                        </span>
                        <span>Data Pelanggan</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.users.index') }}">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:shield-user-bold-duotone"></iconify-icon>
                        </span>
                        <span>Kelola Pengguna</span>
                    </a>
                </li>
                @endrole

                <!-- ── TRANSAKSI ── -->
                <li class="menu-title mt-2">Transaksi</li>

                <li>
                    <a href="#sidebarReadings" data-bs-toggle="collapse">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:waterdrop-bold-duotone"></iconify-icon>
                        </span>
                        <span>Pencatatan Meter</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarReadings">
                        <ul class="nav-second-level">
                            <li><a href="{{ route('admin.water-readings.index') }}">Input Meter</a></li>
                            <li><a href="{{ route('admin.water-readings.history') }}">Riwayat Pembacaan</a></li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarPayments" data-bs-toggle="collapse">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:wallet-money-bold-duotone"></iconify-icon>
                        </span>
                        <span>Pembayaran</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarPayments">
                        <ul class="nav-second-level">
                            <li><a href="{{ route('admin.payments.index') }}">Konfirmasi Bayar</a></li>
                            <li><a href="{{ route('admin.payments.history') }}">Riwayat Pembayaran</a></li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:settings-minimalistic-bold-duotone"></iconify-icon>
                        </span>
                        <span>Maintenance</span>
                    </a>
                </li>

                <!-- ── KEUANGAN ── -->
                @role('admin')
                <li class="menu-title mt-2">Keuangan</li>

                <li>
                    <a href="#">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:bill-list-bold-duotone"></iconify-icon>
                        </span>
                        <span>Kas Transaksi</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:graph-up-bold-duotone"></iconify-icon>
                        </span>
                        <span>Rekap Keuangan</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                        </span>
                        <span>Tagihan PDAM Pusat</span>
                    </a>
                </li>
                @endrole

            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- Left Sidebar End -->
