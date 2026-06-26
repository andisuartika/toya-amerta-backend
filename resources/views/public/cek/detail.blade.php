@extends('layouts.public', ['title' => 'Detail Pemakaian ' . $waterReading->period_label])

@section('content')

<a href="{{ route('public.cek.history', $customer->customer_number) }}" class="text-decoration-none fs-13 mb-3 d-inline-block">
    <i data-feather="arrow-left" style="width:14px;height:14px;"></i> Kembali ke riwayat
</a>

@php
    $statusBadge = match ($waterReading->payment_status) {
        'lunas'    => ['bg-success-subtle text-success', 'Lunas'],
        'sebagian' => ['bg-warning-subtle text-warning', 'Sebagian'],
        default    => ['bg-danger-subtle text-danger', 'Belum Bayar'],
    };
    $usage = $waterReading->current_reading - $waterReading->previous_reading;
    $totalPaid = $waterReading->paymentRecords->sum('amount_paid');
    $remaining = max(0, $waterReading->total_amount - $totalPaid);
@endphp

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <p class="text-muted fs-12 mb-1">💧 TAGIHAN AIR PDAM DESA SANGKET</p>
                <h5 class="fw-semibold mb-0">{{ $waterReading->period_label }}</h5>
            </div>
            <span class="badge {{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
        </div>

        <div class="row g-2 fs-13 mb-3">
            <div class="col-6 text-muted">Pelanggan</div>
            <div class="col-6 fw-medium">{{ $customer->name }}</div>

            <div class="col-6 text-muted">No. Pelanggan</div>
            <div class="col-6 fw-medium">{{ $customer->customer_number }}</div>

            <div class="col-6 text-muted">Tanggal Catat</div>
            <div class="col-6 fw-medium">{{ $waterReading->reading_date?->translatedFormat('d F Y') }}</div>
        </div>

        <hr>

        <div class="row g-2 fs-13 mb-3">
            <div class="col-6 text-muted">Meter Lalu</div>
            <div class="col-6 fw-medium">{{ number_format($waterReading->previous_reading, 1) }} m³</div>

            <div class="col-6 text-muted">Meter Ini</div>
            <div class="col-6 fw-medium">{{ number_format($waterReading->current_reading, 1) }} m³</div>

            <div class="col-6 text-muted">Pemakaian</div>
            <div class="col-6 fw-medium">{{ number_format($usage, 1) }} m³</div>

            <div class="col-6 text-muted">Harga/m³</div>
            <div class="col-6 fw-medium">Rp {{ number_format($waterReading->price_per_m3, 0, ',', '.') }}</div>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-semibold">TOTAL TAGIHAN</span>
            <span class="fw-bold fs-18 text-primary">Rp {{ number_format($waterReading->total_amount, 0, ',', '.') }}</span>
        </div>

        @if ($totalPaid > 0)
            <div class="d-flex justify-content-between fs-13 text-muted">
                <span>Sudah Dibayar</span>
                <span>Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
            </div>
        @endif

        @if ($remaining > 0)
            <div class="d-flex justify-content-between fs-13 text-danger fw-medium">
                <span>Sisa Tagihan</span>
                <span>Rp {{ number_format($remaining, 0, ',', '.') }}</span>
            </div>
        @endif

        @if ($waterReading->photo_url)
            <hr>
            <p class="fw-medium fs-13 mb-2">Foto Meter</p>
            <img src="{{ url($waterReading->photo_url) }}" alt="Foto meter" class="img-fluid rounded">
        @endif
    </div>
</div>

@if ($waterReading->paymentRecords->isNotEmpty())
<div class="card border-0 shadow-sm mt-3">
    <div class="card-body p-4">
        <h6 class="fw-semibold mb-3">Riwayat Pembayaran</h6>
        @foreach ($waterReading->paymentRecords as $payment)
            <div class="d-flex justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <div class="fw-medium fs-13">{{ $payment->receipt_number }}</div>
                    <div class="text-muted fs-12">{{ $payment->payment_date?->translatedFormat('d F Y') }} · {{ strtoupper($payment->payment_method) }}</div>
                </div>
                <div class="fw-semibold fs-13">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</div>
            </div>
        @endforeach
    </div>
</div>
@endif

@if ($remaining > 0)
<div class="alert alert-warning fs-13 mt-3 mb-0">
    Mohon segera melakukan pembayaran ke petugas PDAM terdekat. Terima kasih 🙏
</div>
@endif

@endsection

@section('script-bottom')
<script>if (typeof feather !== 'undefined') feather.replace();</script>
@endsection
