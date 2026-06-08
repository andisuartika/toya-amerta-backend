@extends('layouts.vertical', ['title' => 'Riwayat Pembacaan Meteran'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Riwayat Pembacaan Meteran</h4>
            <p class="text-muted mb-0 fs-13">Histori pencatatan per pelanggan</p>
        </div>
        <a href="{{ route('admin.water-readings.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i data-feather="arrow-left" style="width:14px;height:14px;" class="me-1"></i>Input Meteran
        </a>
    </div>

    {{-- Filter Pelanggan --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-6">
                    <label class="form-label mb-1 fs-13">Pilih Pelanggan</label>
                    <select name="customer_id" id="selectCustomerHistory" class="form-select form-select-sm">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->customer_number }} – {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if ($customer)
    {{-- Info Pelanggan --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-3 align-items-center">
                <div class="col-sm-auto">
                    <span class="badge bg-primary-subtle text-primary fw-semibold font-monospace fs-13 px-3 py-2">
                        {{ $customer->customer_number }}
                    </span>
                </div>
                <div class="col-sm">
                    <div class="fw-semibold">{{ $customer->name }}</div>
                    <div class="fs-12 text-muted">
                        {{ $customer->zone->name ?? '—' }} |
                        Tarif: {{ $customer->tariffRate->name ?? '—' }} |
                        Pasang: {{ $customer->installation_date?->format('d M Y') }}
                    </div>
                </div>
                <div class="col-sm-auto text-sm-end">
                    <div class="fs-12 text-muted">Total tagihan belum bayar</div>
                    <div class="fw-bold text-danger fs-16">
                        Rp {{ number_format($readings->where('payment_status', 'belum_bayar')->sum('total_amount'), 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fs-15">
                Riwayat 24 Bulan Terakhir
                <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $readings->count() }} periode</span>
            </h5>
            <input type="text" id="historySearch" class="form-control form-control-sm"
                   placeholder="Cari..." style="width:180px">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="historyTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Periode</th>
                            <th>Tgl Baca</th>
                            <th class="text-end">Meter Lalu</th>
                            <th class="text-end">Meter Kini</th>
                            <th class="text-end">Pakai (m³)</th>
                            <th class="text-end">Tagihan</th>
                            <th class="text-center" style="width:100px">Status</th>
                            <th class="ps-2">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($readings as $r)
                        <tr>
                            <td class="ps-3 fw-medium">{{ $r->period_label }}</td>
                            <td class="text-muted fs-13">{{ $r->reading_date->format('d M Y') }}</td>
                            <td class="text-end">{{ number_format($r->previous_reading, 1) }}</td>
                            <td class="text-end">{{ number_format($r->current_reading, 1) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($r->usage_m3, 1) }}</td>
                            <td class="text-end fw-semibold text-primary">
                                Rp {{ number_format($r->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if ($r->payment_status === 'lunas')
                                    <span class="badge bg-success-subtle text-success">Lunas</span>
                                @elseif ($r->payment_status === 'sebagian')
                                    <span class="badge bg-warning-subtle text-warning">Sebagian</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="fs-13 text-muted">{{ $r->officer->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">Belum ada riwayat pembacaan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if ($readings->count())
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="ps-3 text-end fw-semibold">Total:</td>
                            <td class="text-end fw-bold">{{ number_format($readings->sum('usage_m3'), 1) }} m³</td>
                            <td class="text-end fw-bold text-primary">
                                Rp {{ number_format($readings->sum('total_amount'), 0, ',', '.') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i data-feather="search" style="width:40px;height:40px;" class="mb-3 opacity-50"></i>
            <p>Pilih pelanggan untuk melihat riwayat pembacaan.</p>
        </div>
    </div>
    @endif

</div>
@endsection

@section('script-bottom')
<script>
window.addEventListener('load', function () {
    // Select2 pilih pelanggan
    var $sel = $('#selectCustomerHistory');
    $sel.select2({
        placeholder: '-- Cari nama / nomor pelanggan --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () { return 'Pelanggan tidak ditemukan'; },
            searching: function () { return 'Mencari...'; },
        },
    });

    // select2:select / select2:unselect hanya terpicu dari interaksi user, bukan saat init
    $sel.on('select2:select select2:unselect', function () {
        this.form.submit();
    });

    // Search tabel riwayat
    document.getElementById('historySearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#historyTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
});
</script>
@endsection
