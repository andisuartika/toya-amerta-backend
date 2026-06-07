@extends('layouts.vertical', ['title' => 'Dashboard'])

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
        </div>
        <div class="text-end">
            <small class="text-muted">{{ now()->isoFormat('dddd, D MMMM Y') }}</small>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-primary rounded fs-24">
                                <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="text-primary"></iconify-icon>
                            </span>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <p class="text-muted mb-0 fs-13">Total Pelanggan Aktif</p>
                            <h4 class="mb-0 fw-semibold">{{ number_format($stats['total_customers']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-success rounded fs-24">
                                <iconify-icon icon="solar:map-point-bold-duotone" class="text-success"></iconify-icon>
                            </span>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <p class="text-muted mb-0 fs-13">Zona Wilayah</p>
                            <h4 class="mb-0 fw-semibold">{{ $stats['total_zones'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-warning rounded fs-24">
                                <iconify-icon icon="solar:tag-price-bold-duotone" class="text-warning"></iconify-icon>
                            </span>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <p class="text-muted mb-0 fs-13">Jenis Tarif</p>
                            <h4 class="mb-0 fw-semibold">{{ $stats['total_tariffs'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info rounded fs-24">
                                <iconify-icon icon="solar:shield-user-bold-duotone" class="text-info"></iconify-icon>
                            </span>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <p class="text-muted mb-0 fs-13">Petugas Aktif</p>
                            <h4 class="mb-0 fw-semibold">{{ $stats['total_officers'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Akses Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.zones.index') }}" class="text-decoration-none">
                                <div class="p-3 border rounded-3 text-center hover-shadow transition-all">
                                    <iconify-icon icon="solar:map-point-bold-duotone" class="fs-32 text-primary mb-2 d-block"></iconify-icon>
                                    <p class="mb-0 fw-medium fs-13">Zona Wilayah</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.tariff-rates.index') }}" class="text-decoration-none">
                                <div class="p-3 border rounded-3 text-center">
                                    <iconify-icon icon="solar:tag-price-bold-duotone" class="fs-32 text-warning mb-2 d-block"></iconify-icon>
                                    <p class="mb-0 fw-medium fs-13">Tarif Air</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.customers.index') }}" class="text-decoration-none">
                                <div class="p-3 border rounded-3 text-center">
                                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="fs-32 text-success mb-2 d-block"></iconify-icon>
                                    <p class="mb-0 fw-medium fs-13">Data Pelanggan</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                                <div class="p-3 border rounded-3 text-center">
                                    <iconify-icon icon="solar:shield-user-bold-duotone" class="fs-32 text-info mb-2 d-block"></iconify-icon>
                                    <p class="mb-0 fw-medium fs-13">Pengguna</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
