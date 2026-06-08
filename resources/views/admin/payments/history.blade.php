@extends('layouts.vertical', ['title' => 'Riwayat Pembayaran'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Riwayat Pembayaran</h4>
            <p class="text-muted mb-0 fs-13">Histori semua transaksi pembayaran</p>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i data-feather="arrow-left" style="width:14px;height:14px;" class="me-1"></i>Konfirmasi Bayar
        </a>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-4">
                    <label class="form-label mb-1 fs-13">Pelanggan</label>
                    <select name="customer_id" id="selectCustomerHistory" class="form-select form-select-sm">
                        <option value="">Semua Pelanggan</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" {{ ($filters['customer_id'] ?? '') == $c->id ? 'selected' : '' }}>
                                {{ $c->customer_number }} – {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i data-feather="filter" style="width:13px;height:13px;" class="me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.payments.history') }}" class="btn btn-outline-secondary btn-sm px-4 ms-1">
                        <i data-feather="x" style="width:13px;height:13px;" class="me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fs-15">
                Riwayat Pembayaran
                <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $payments->count() }}</span>
            </h5>
            <input type="text" id="historySearch" class="form-control form-control-sm"
                   placeholder="Cari..." style="width:180px">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="historyTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">No. Kwitansi</th>
                            <th>Pelanggan</th>
                            <th class="text-center">Periode</th>
                            <th>Tgl Bayar</th>
                            <th>Metode</th>
                            <th class="text-end">Dibayar</th>
                            <th class="text-center" style="width:90px">Status</th>
                            <th>Petugas</th>
                            <th class="text-end pe-3" style="width:70px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $p)
                        <tr>
                            <td class="ps-3">
                                <span class="font-monospace fs-12 text-muted">{{ $p->receipt_number ?? '—' }}</span>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $p->customer->name }}</div>
                                <div class="fs-12 text-muted">{{ $p->customer->customer_number }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary fw-medium">
                                    {{ \Carbon\Carbon::create()->month($p->waterReading->period_month)->translatedFormat('M') }}
                                    {{ $p->waterReading->period_year }}
                                </span>
                            </td>
                            <td class="fs-13">{{ $p->payment_date->format('d M Y') }}</td>
                            <td class="fs-13">
                                @if ($p->payment_method === 'tunai')
                                    <span class="badge bg-success-subtle text-success">Tunai</span>
                                @elseif ($p->payment_method === 'transfer')
                                    <span class="badge bg-info-subtle text-info">Transfer</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Lainnya</span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold text-success">
                                Rp {{ number_format($p->amount_paid, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if ($p->status === 'lunas')
                                    <span class="badge bg-success-subtle text-success">Lunas</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">Sebagian</span>
                                @endif
                            </td>
                            <td class="fs-12 text-muted">{{ $p->recordedBy->name ?? '—' }}</td>
                            <td class="text-end pe-3">
                                <a class="btn btn-sm bg-danger-subtle btn-delete" role="button"
                                   data-id="{{ $p->id }}"
                                   data-name="{{ $p->customer->name }} ({{ $p->receipt_number }})"
                                   title="Batalkan">
                                    <i data-feather="trash-2" style="width:13px;height:13px;" class="text-danger"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">Belum ada riwayat pembayaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if ($payments->count())
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="ps-3 text-end fw-semibold">Total diterima:</td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($payments->sum('amount_paid'), 0, ',', '.') }}
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.modal-delete', [
    'entity'  => 'Pembayaran',
    'warning' => 'Membatalkan pembayaran akan mengembalikan status tagihan ke belum bayar.',
])
@endsection

@section('script-bottom')
<script>
window.addEventListener('load', function () {
    var $sel = $('#selectCustomerHistory');
    $sel.select2({
        placeholder: '-- Semua Pelanggan --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () { return 'Pelanggan tidak ditemukan'; },
            searching: function () { return 'Mencari...'; },
        },
    });
    $sel.on('select2:select select2:unselect', function () {
        this.form.submit();
    });

    document.getElementById('historySearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#historyTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/payments/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endsection
