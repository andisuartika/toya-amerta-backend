@extends('layouts.public', ['title' => 'Riwayat Pencatatan — ' . $customer->name])

@section('content')

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-4">
        <p class="text-muted fs-12 mb-1">No. Pelanggan</p>
        <h5 class="fw-semibold mb-3">{{ $customer->customer_number }}</h5>

        <div class="row g-2 fs-13">
            <div class="col-6 text-muted">Nama</div>
            <div class="col-6 fw-medium">{{ $customer->name }}</div>

            <div class="col-6 text-muted">Alamat</div>
            <div class="col-6 fw-medium">{{ $customer->address }}</div>

            <div class="col-6 text-muted">Zona</div>
            <div class="col-6 fw-medium">{{ $customer->zone?->name ?? '—' }}</div>

            <div class="col-6 text-muted">Golongan Tarif</div>
            <div class="col-6 fw-medium">{{ $customer->tariffRate?->name ?? '—' }}</div>

            <div class="col-6 text-muted">Status</div>
            <div class="col-6">
                @if ($customer->is_active)
                    <span class="badge bg-success-subtle text-success">Aktif</span>
                @else
                    <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h6 class="fw-semibold mb-3">Riwayat Pencatatan Meter</h6>

        @forelse ($readings as $reading)
            @php
                $statusBadge = match ($reading->payment_status) {
                    'lunas'    => ['bg-success-subtle text-success', 'Lunas'],
                    'sebagian' => ['bg-warning-subtle text-warning', 'Sebagian'],
                    default    => ['bg-danger-subtle text-danger', 'Belum Bayar'],
                };
            @endphp
            <a href="{{ route('public.cek.detail', [$customer->customer_number, $reading->id]) }}"
               class="d-flex justify-content-between align-items-center text-decoration-none text-dark py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <div class="fw-medium">{{ $reading->period_label }}</div>
                    <div class="text-muted fs-12">Pemakaian {{ number_format($reading->current_reading - $reading->previous_reading, 1) }} m³</div>
                </div>
                <div class="text-end">
                    <div class="fw-semibold">Rp {{ number_format($reading->total_amount, 0, ',', '.') }}</div>
                    <span class="badge {{ $statusBadge[0] }} fs-11">{{ $statusBadge[1] }}</span>
                </div>
            </a>
        @empty
            <p class="text-muted fs-13 text-center py-4 mb-0">Belum ada riwayat pencatatan meter.</p>
        @endforelse
    </div>
</div>

@endsection
