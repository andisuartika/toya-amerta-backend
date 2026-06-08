@extends('layouts.vertical', ['title' => 'Konfirmasi Pembayaran'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Konfirmasi Pembayaran</h4>
            <p class="text-muted mb-0 fs-13">Tagihan air yang belum & sebagian terbayar</p>
        </div>
        <a href="{{ route('admin.payments.history') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i data-feather="clock" style="width:14px;height:14px;" class="me-1"></i>Riwayat Pembayaran
        </a>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label mb-1 fs-13">Zona</label>
                    <select name="zone_id" class="form-select form-select-sm">
                        <option value="">Semua Zona</option>
                        @foreach ($zones as $z)
                            <option value="{{ $z->id }}" {{ ($filters['zone_id'] ?? '') == $z->id ? 'selected' : '' }}>
                                {{ $z->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Bulan</label>
                    <select name="month" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ ($filters['month'] ?? '') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Tahun</label>
                    <select name="year" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach(range(now()->year, 2020) as $y)
                            <option value="{{ $y }}" {{ ($filters['year'] ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i data-feather="filter" style="width:13px;height:13px;" class="me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm px-4 ms-1">
                        <i data-feather="x" style="width:13px;height:13px;" class="me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary --}}
    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="card border-0 bg-danger-subtle">
                <div class="card-body py-3">
                    <div class="fs-12 text-muted mb-1">Total Belum Bayar</div>
                    <div class="fs-20 fw-bold text-danger">
                        Rp {{ number_format($unpaid->where('payment_status','belum_bayar')->sum('remaining_amount'), 0, ',', '.') }}
                    </div>
                    <div class="fs-12 text-muted">{{ $unpaid->where('payment_status','belum_bayar')->count() }} tagihan</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 bg-warning-subtle">
                <div class="card-body py-3">
                    <div class="fs-12 text-muted mb-1">Bayar Sebagian</div>
                    <div class="fs-20 fw-bold text-warning">
                        Rp {{ number_format($unpaid->where('payment_status','sebagian')->sum('remaining_amount'), 0, ',', '.') }}
                    </div>
                    <div class="fs-12 text-muted">{{ $unpaid->where('payment_status','sebagian')->count() }} tagihan</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 bg-primary-subtle">
                <div class="card-body py-3">
                    <div class="fs-12 text-muted mb-1">Total Piutang</div>
                    <div class="fs-20 fw-bold text-primary">
                        Rp {{ number_format($unpaid->sum('remaining_amount'), 0, ',', '.') }}
                    </div>
                    <div class="fs-12 text-muted">{{ $unpaid->count() }} tagihan pending</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Tagihan --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fs-15">Daftar Tagihan Belum Lunas</h5>
            <input type="text" id="paymentSearch" class="form-control form-control-sm"
                   placeholder="Cari pelanggan..." style="width:200px">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="paymentTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Pelanggan</th>
                            <th class="text-center" style="width:110px">Periode</th>
                            <th class="text-end">Tagihan</th>
                            <th class="text-center" style="width:100px">Status</th>
                            <th class="text-end pe-3" style="width:90px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unpaid as $r)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-medium">{{ $r->customer->name }}</div>
                                <div class="fs-12 text-muted">
                                    {{ $r->customer->customer_number }} · {{ $r->customer->zone->name ?? '—' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary fw-medium">
                                    {{ \Carbon\Carbon::create()->month($r->period_month)->translatedFormat('M') }}
                                    {{ $r->period_year }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-danger fs-14">
                                Rp {{ number_format($r->remaining_amount, 0, ',', '.') }}
                                @if ($r->payment_status === 'sebagian')
                                    <div class="fs-11 text-muted fw-normal">
                                        dari Rp {{ number_format($r->total_amount, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($r->payment_status === 'sebagian')
                                    <span class="badge bg-warning-subtle text-warning">Sebagian</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-primary btn-konfirmasi"
                                    data-id="{{ $r->id }}"
                                    data-name="{{ $r->customer->name }}"
                                    data-period="{{ \Carbon\Carbon::create()->month($r->period_month)->translatedFormat('F') }} {{ $r->period_year }}"
                                    data-total="{{ $r->remaining_amount }}"
                                    data-original="{{ $r->total_amount }}"
                                    data-bs-toggle="modal" data-bs-target="#modalKonfirmasi"
                                    title="Konfirmasi Bayar">
                                    <i data-feather="check-circle" style="width:13px;height:13px;" class="me-1"></i>Bayar
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i data-feather="check-circle" style="width:32px;height:32px;" class="mb-2 text-success opacity-50 d-block mx-auto"></i>
                                Semua tagihan sudah lunas!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Bayar --}}
<div class="modal fade" id="modalKonfirmasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.payments.store') }}" id="formKonfirmasi">
                @csrf
                <input type="hidden" name="water_reading_id" id="konfirmasiReadingId">

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Konfirmasi Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- Info tagihan --}}
                    <div class="rounded-3 bg-light border p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold fs-14" id="konfirmasiNama"></div>
                                <div class="text-muted fs-12 mt-1" id="konfirmasiPeriode"></div>
                            </div>
                            <div class="text-end">
                                <div class="fs-11 text-muted" id="konfirmasiTotalLabel">Total Tagihan</div>
                                <div class="fw-bold fs-18 text-danger" id="konfirmasiTotal"></div>
                                <div class="fs-11 text-muted" id="konfirmasiOriginal"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Jumlah Bayar <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text fw-semibold">Rp</span>
                            <input type="text" id="amountPaidDisplay"
                                   class="form-control fw-bold" required
                                   placeholder="0" inputmode="numeric" autocomplete="off">
                            <input type="hidden" name="amount_paid" id="amountPaid">
                        </div>
                        <div class="form-text">Isi sesuai tagihan untuk lunas, atau kurang untuk sebagian.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" id="paymentDate"
                                   class="form-control" required value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Metode <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="tunai" selected>Tunai (Cash)</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-medium">Catatan</label>
                        <input type="text" name="notes" class="form-control" placeholder="Opsional">
                    </div>

                    {{-- Preview status --}}
                    <div class="mt-3 fs-13" id="statusPreview"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i data-feather="check" style="width:14px;height:14px;" class="me-1"></i>Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script-bottom')
<script>
(function () {
    // Bootstrap 5: pakai event show.bs.modal + relatedTarget
    // Ini cara resmi Bootstrap untuk passing data ke modal dari trigger element
    var modalEl = document.getElementById('modalKonfirmasi');

    modalEl.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget; // tombol yang men-trigger modal
        var d   = btn.dataset;

        document.getElementById('konfirmasiReadingId').value     = d.id;
        document.getElementById('konfirmasiNama').textContent    = d.name;
        document.getElementById('konfirmasiPeriode').textContent = 'Periode: ' + d.period;

        var remaining = Math.round(parseFloat(d.total));
        var original  = Math.round(parseFloat(d.original));
        document.getElementById('konfirmasiTotal').textContent = 'Rp ' + remaining.toLocaleString('id-ID');
        document.getElementById('konfirmasiTotalLabel').textContent = remaining < original ? 'Sisa Tagihan' : 'Total Tagihan';
        document.getElementById('konfirmasiOriginal').textContent   = remaining < original
            ? 'dari Rp ' + original.toLocaleString('id-ID')
            : '';

        var total    = Math.round(parseFloat(d.total));
        var display  = document.getElementById('amountPaidDisplay');
        var hidden   = document.getElementById('amountPaid');
        display.value         = formatRibuan(total);
        hidden.value          = total;
        display.dataset.total = total;
        display.dataset.max   = total;

        updateStatusPreview();
    });

    function formatRibuan(val) {
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function parseRibuan(val) {
        return parseInt(val.replace(/\./g, ''), 10) || 0;
    }

    document.getElementById('amountPaidDisplay').addEventListener('input', function () {
        var raw    = this.value.replace(/[^\d]/g, '');
        var num    = parseInt(raw, 10) || 0;
        var max    = parseInt(this.dataset.max, 10) || 0;
        var cursor = this.selectionStart;
        var oldLen = this.value.length;

        // Batasi tidak boleh melebihi sisa tagihan
        if (max > 0 && num > max) {
            num = max;
            raw = String(num);
        }

        this.value = raw ? formatRibuan(num) : '';
        document.getElementById('amountPaid').value = num || '';

        var newLen = this.value.length;
        this.setSelectionRange(cursor + (newLen - oldLen), cursor + (newLen - oldLen));

        updateStatusPreview();
    });

    function updateStatusPreview() {
        var paid  = parseRibuan(document.getElementById('amountPaidDisplay').value);
        var total = parseFloat(document.getElementById('amountPaidDisplay').dataset.total) || 0;
        var el    = document.getElementById('statusPreview');
        if (!total) { el.innerHTML = ''; return; }

        if (paid >= total) {
            el.innerHTML = '<span class="badge bg-success-subtle text-success fs-13 px-3 py-2">✓ LUNAS</span>';
        } else if (paid > 0) {
            var sisa = total - paid;
            el.innerHTML = '<span class="badge bg-warning-subtle text-warning fs-13 px-3 py-2">' +
                'Sebagian — sisa Rp ' + sisa.toLocaleString('id-ID') + '</span>';
        } else {
            el.innerHTML = '';
        }
    }

    // Search
    document.getElementById('paymentSearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#paymentTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}());
</script>
@endsection
