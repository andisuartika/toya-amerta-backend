@extends('layouts.vertical', ['title' => 'Tagihan PDAM Pusat'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Tagihan PDAM Pusat</h4>
            <p class="text-muted mb-0 fs-13">Kelola tagihan air dari PDAM Pusat ke desa</p>
        </div>
        <button class="btn btn-primary btn-sm px-3 mt-2 mt-sm-0" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i data-feather="plus" style="width:14px;height:14px;" class="me-1"></i>Tambah Tagihan
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 fs-13">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 fs-13">{{ $errors->first() }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-primary-subtle">
                <div class="card-body py-3">
                    <div class="fs-12 text-muted mb-1">Total Tagihan</div>
                    <div class="fw-bold fs-15 text-primary">Rp {{ number_format($summary['total_tagihan'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-danger-subtle">
                <div class="card-body py-3">
                    <div class="fs-12 text-muted mb-1">Belum Bayar</div>
                    <div class="fw-bold fs-15 text-danger">Rp {{ number_format($summary['belum_bayar'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-success-subtle">
                <div class="card-body py-3">
                    <div class="fs-12 text-muted mb-1">Sudah Lunas</div>
                    <div class="fw-bold fs-15 text-success">Rp {{ number_format($summary['lunas'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 {{ $summary['jatuh_tempo'] > 0 ? 'bg-warning-subtle' : 'bg-light' }}">
                <div class="card-body py-3">
                    <div class="fs-12 text-muted mb-1">Lewat Jatuh Tempo</div>
                    <div class="fw-bold fs-15 {{ $summary['jatuh_tempo'] > 0 ? 'text-warning' : 'text-muted' }}">{{ $summary['jatuh_tempo'] }} tagihan</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                <label class="fs-13 fw-semibold mb-0">Tahun:</label>
                <select name="year" class="form-select form-select-sm" style="width:110px" onchange="this.form.submit()">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
                <label class="fs-13 fw-semibold mb-0 ms-2">Status:</label>
                <select name="status" class="form-select form-select-sm" style="width:140px" onchange="this.form.submit()">
                    <option value="">-- Semua --</option>
                    <option value="belum_bayar" @selected(request('status') === 'belum_bayar')>Belum Bayar</option>
                    <option value="lunas"       @selected(request('status') === 'lunas')>Lunas</option>
                </select>
                @if (request('status'))
                    <a href="{{ route('admin.pdam-bills.index', ['year' => $year]) }}" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="x" style="width:13px;height:13px;" class="me-1"></i>Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fs-15">Daftar Tagihan — {{ $year }}</h5>
            <input type="text" id="billSearch" class="form-control form-control-sm" placeholder="Cari..." style="width:200px">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 fs-13" id="billTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Periode</th>
                            <th>No. Invoice</th>
                            <th class="text-end">Total m³</th>
                            <th>Rincian Tarif</th>
                            <th class="text-end">Biaya Tambahan</th>
                            <th class="text-end">Total Tagihan</th>
                            <th>Tgl Tagihan</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Tgl Bayar</th>
                            <th class="text-end pe-3" style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bills as $bill)
                        <tr class="{{ $bill->is_overdue ? 'table-warning' : '' }}">
                            <td class="ps-3 fw-semibold">{{ $bill->period_label }}</td>
                            <td class="text-muted">{{ $bill->invoice_number ?? '—' }}</td>
                            <td class="text-end">{{ number_format($bill->total_m3_from_pdam, 2, ',', '.') }} m³</td>
                            <td>
                                @if ($bill->tier_details)
                                    @foreach ($bill->tier_details as $tier)
                                        <div class="fs-11 text-muted">
                                            {{ $tier['label'] }}: {{ number_format($tier['volume'], 2, ',', '.') }} m³
                                            × Rp {{ number_format($tier['price'], 0, ',', '.') }}
                                            = <span class="text-dark fw-semibold">Rp {{ number_format($tier['subtotal'], 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">{{ $bill->additional_charge > 0 ? 'Rp '.number_format($bill->additional_charge, 0, ',', '.') : '—' }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($bill->total_bill_amount, 0, ',', '.') }}</td>
                            <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
                            <td class="{{ $bill->is_overdue ? 'text-danger fw-semibold' : '' }}">
                                {{ $bill->due_date->format('d/m/Y') }}
                                @if ($bill->is_overdue)
                                    <span class="badge bg-danger-subtle text-danger fs-10 ms-1">Terlambat</span>
                                @endif
                            </td>
                            <td>
                                @if ($bill->payment_status === 'lunas')
                                    <span class="badge bg-success-subtle text-success">Lunas</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $bill->payment_date ? $bill->payment_date->format('d/m/Y') : '—' }}</td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-1">
                                    @if ($bill->payment_status === 'belum_bayar')
                                    <button class="btn btn-sm bg-success-subtle btn-konfirmasi"
                                        data-id="{{ $bill->id }}"
                                        data-label="{{ $bill->period_label }}"
                                        data-amount="{{ number_format($bill->total_bill_amount, 0, ',', '.') }}"
                                        title="Konfirmasi Lunas">
                                        <i data-feather="check-circle" style="width:13px;height:13px;" class="text-success"></i>
                                    </button>
                                    @endif
                                    <button class="btn btn-sm bg-primary-subtle btn-edit"
                                        data-id="{{ $bill->id }}"
                                        data-year="{{ $bill->period_year }}"
                                        data-month="{{ $bill->period_month }}"
                                        data-tiers="{{ json_encode($bill->tier_details ?? []) }}"
                                        data-additional="{{ (int) $bill->additional_charge }}"
                                        data-total="{{ (int) $bill->total_bill_amount }}"
                                        data-billdate="{{ $bill->bill_date->format('Y-m-d') }}"
                                        data-duedate="{{ $bill->due_date->format('Y-m-d') }}"
                                        data-invoice="{{ $bill->invoice_number }}"
                                        data-notes="{{ $bill->notes }}"
                                        title="Edit">
                                        <i data-feather="edit-2" style="width:13px;height:13px;" class="text-primary"></i>
                                    </button>
                                    <button class="btn btn-sm bg-danger-subtle btn-delete"
                                        data-id="{{ $bill->id }}"
                                        data-name="Tagihan {{ $bill->period_label }}"
                                        title="Hapus">
                                        <i data-feather="trash-2" style="width:13px;height:13px;" class="text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i data-feather="inbox" style="width:32px;height:32px;" class="mb-2 opacity-50 d-block mx-auto"></i>
                                Belum ada tagihan untuk tahun {{ $year }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Template baris tier (tersembunyi) --}}
<template id="tierRowTemplate">
    <tr class="tier-row">
        <td>
            <input type="text" name="tier_label[]" class="form-control form-control-sm"
                placeholder="cth: 0–10 m³" style="min-width:110px">
        </td>
        <td>
            <input type="number" name="tier_volume[]" class="form-control form-control-sm tier-volume"
                placeholder="0.00" step="0.01" min="0" style="min-width:80px">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm input-ribuan tier-price-display"
                placeholder="0" style="min-width:110px">
            <input type="hidden" name="tier_price[]" class="tier-price-hidden">
        </td>
        <td class="tier-subtotal text-end fw-semibold fs-13 align-middle">—</td>
        <td class="align-middle">
            <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-tier">
                <i data-feather="x-circle" style="width:15px;height:15px;"></i>
            </button>
        </td>
    </tr>
</template>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.pdam-bills.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fs-15">Tambah Tagihan PDAM Pusat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">Periode <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <select name="period_month" class="form-select form-select-sm" required>
                                    @foreach ($monthNames as $num => $name)
                                        <option value="{{ $num }}" @selected($num == now()->month)>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="period_year" class="form-control form-control-sm" style="width:90px"
                                    value="{{ now()->year }}" min="2000" max="2100" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">No. Invoice</label>
                            <input type="text" name="invoice_number" class="form-control form-control-sm" placeholder="Opsional">
                        </div>

                        {{-- Tier table --}}
                        <div class="col-12">
                            <label class="form-label fs-13 fw-semibold">Rincian Tarif per Blok <span class="text-danger">*</span></label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered fs-13 mb-1" id="tierTableTambah">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Keterangan Blok</th>
                                            <th style="width:110px">Volume (m³)</th>
                                            <th style="width:140px">Harga/m³ (Rp)</th>
                                            <th style="width:120px" class="text-end">Subtotal</th>
                                            <th style="width:40px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tierBodyTambah"></tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-add-tier" data-target="tierBodyTambah">
                                <i data-feather="plus" style="width:13px;height:13px;" class="me-1"></i>Tambah Blok
                            </button>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">Biaya Tambahan (Rp)</label>
                            <input type="text" class="form-control form-control-sm input-ribuan trigger-total" id="t_add_display" placeholder="0">
                            <input type="hidden" name="additional_charge" id="t_add" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">Total Tagihan (Rp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm input-ribuan" id="t_total_display" placeholder="Otomatis">
                            <input type="hidden" name="total_bill_amount" id="t_total">
                            <small class="text-muted fs-11">Dihitung otomatis dari subtotal tier + biaya tambahan. Bisa diedit manual.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">Tanggal Tagihan <span class="text-danger">*</span></label>
                            <input type="date" name="bill_date" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fs-13 fw-semibold">Catatan</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Opsional"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="formEdit">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fs-15">Edit Tagihan — <span id="e_period_label"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">No. Invoice</label>
                            <input type="text" name="invoice_number" id="e_invoice" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label fs-13 fw-semibold">Rincian Tarif per Blok <span class="text-danger">*</span></label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered fs-13 mb-1" id="tierTableEdit">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Keterangan Blok</th>
                                            <th style="width:110px">Volume (m³)</th>
                                            <th style="width:140px">Harga/m³ (Rp)</th>
                                            <th style="width:120px" class="text-end">Subtotal</th>
                                            <th style="width:40px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tierBodyEdit"></tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-add-tier" data-target="tierBodyEdit">
                                <i data-feather="plus" style="width:13px;height:13px;" class="me-1"></i>Tambah Blok
                            </button>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">Biaya Tambahan (Rp)</label>
                            <input type="text" class="form-control form-control-sm input-ribuan trigger-total" id="e_add_display">
                            <input type="hidden" name="additional_charge" id="e_add" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">Total Tagihan (Rp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm input-ribuan" id="e_total_display">
                            <input type="hidden" name="total_bill_amount" id="e_total">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">Tanggal Tagihan <span class="text-danger">*</span></label>
                            <input type="date" name="bill_date" id="e_billdate" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" id="e_duedate" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fs-13 fw-semibold">Catatan</label>
                            <textarea name="notes" id="e_notes" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Bayar --}}
<div class="modal fade" id="modalKonfirmasi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formKonfirmasi">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title fs-15">Konfirmasi Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fs-13 text-muted mb-3">
                        Konfirmasi pembayaran tagihan <strong id="konfirmasiLabel"></strong>
                        senilai <strong class="text-danger" id="konfirmasiAmount"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold">Tanggal Bayar <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control form-control-sm"
                            value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="form-label fs-13 fw-semibold">Catatan</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Opsional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i data-feather="check" style="width:13px;height:13px;" class="me-1"></i>Konfirmasi Lunas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.partials.modal-delete', [
    'entity'  => 'Tagihan PDAM Pusat',
    'warning' => 'Data tagihan dan catatan kas terkait akan dihapus.',
])
@endsection

@section('script-bottom')
<script>
(function () {
    var monthNames = @json($monthNames);

    // ── Format ribuan ──────────────────────────────────────────────
    function applyRibuan(el) {
        el.addEventListener('input', function () {
            var raw = this.value.replace(/[^\d]/g, '');
            this.value = raw ? parseInt(raw, 10).toLocaleString('id-ID') : '';
            var hiddenId = this.id ? this.id.replace('_display', '') : null;
            if (hiddenId) {
                var hid = document.getElementById(hiddenId);
                if (hid) hid.value = raw || '';
            }
            // jika tier-price-display, update hidden sibling
            if (this.classList.contains('tier-price-display')) {
                var hid = this.nextElementSibling;
                if (hid) hid.value = raw || '';
                recalcRow(this.closest('tr'));
                recalcTotal(this.closest('tbody').id);
            }
            if (this.classList.contains('trigger-total')) {
                var prefix = this.id.startsWith('t_') ? 't' : 'e';
                recalcGrandTotal(prefix);
            }
        });
    }

    document.querySelectorAll('.input-ribuan').forEach(applyRibuan);

    // ── Tier row helpers ──────────────────────────────────────────
    function recalcRow(row) {
        var vol   = parseFloat(row.querySelector('.tier-volume')?.value || 0);
        var price = parseFloat(row.querySelector('.tier-price-hidden')?.value || 0);
        var sub   = vol * price;
        var cell  = row.querySelector('.tier-subtotal');
        if (cell) cell.textContent = sub > 0 ? 'Rp ' + Math.round(sub).toLocaleString('id-ID') : '—';
        row.dataset.subtotal = sub;
    }

    function recalcTotal(bodyId) {
        var prefix = bodyId === 'tierBodyTambah' ? 't' : 'e';
        var total = 0;
        document.querySelectorAll('#' + bodyId + ' tr').forEach(function (row) {
            var vol   = parseFloat(row.querySelector('.tier-volume')?.value || 0);
            var price = parseFloat(row.querySelector('.tier-price-hidden')?.value || 0);
            total += vol * price;
        });
        // store base in hidden for grand total calc
        document.getElementById(prefix + '_tier_base') && (document.getElementById(prefix + '_tier_base').value = Math.round(total));
        recalcGrandTotal(prefix);
    }

    function recalcGrandTotal(prefix) {
        var tBase   = 0;
        var bodyId  = prefix === 't' ? 'tierBodyTambah' : 'tierBodyEdit';
        document.querySelectorAll('#' + bodyId + ' tr').forEach(function (row) {
            var vol   = parseFloat(row.querySelector('.tier-volume')?.value || 0);
            var price = parseFloat(row.querySelector('.tier-price-hidden')?.value || 0);
            tBase += vol * price;
        });
        var add    = parseFloat(document.getElementById(prefix + '_add')?.value || 0);
        var grand  = Math.round(tBase) + add;
        document.getElementById(prefix + '_total_display').value = grand > 0 ? grand.toLocaleString('id-ID') : '';
        document.getElementById(prefix + '_total').value = grand > 0 ? grand : '';
    }

    function buildTierRow(data) {
        var tmpl = document.getElementById('tierRowTemplate');
        var frag = tmpl.content.cloneNode(true);
        var row  = frag.querySelector('tr');

        if (data) {
            row.querySelector('[name="tier_label[]"]').value = data.label || '';
            row.querySelector('.tier-volume').value = data.volume || '';
            var priceRaw = data.price || 0;
            row.querySelector('.tier-price-display').value = priceRaw ? parseInt(priceRaw).toLocaleString('id-ID') : '';
            row.querySelector('.tier-price-hidden').value  = priceRaw;
            var sub = (data.volume || 0) * priceRaw;
            row.querySelector('.tier-subtotal').textContent = sub > 0 ? 'Rp ' + Math.round(sub).toLocaleString('id-ID') : '—';
        }
        return row;
    }

    function attachRowEvents(row, bodyId) {
        row.querySelector('.tier-volume')?.addEventListener('input', function () {
            recalcRow(this.closest('tr'));
            recalcTotal(bodyId);
        });
        row.querySelector('.tier-price-display') && applyRibuan(row.querySelector('.tier-price-display'));
        row.querySelector('.btn-remove-tier')?.addEventListener('click', function () {
            this.closest('tr').remove();
            recalcTotal(bodyId);
        });
        if (typeof feather !== 'undefined') feather.replace();
    }

    // ── Add tier button ───────────────────────────────────────────
    document.querySelectorAll('.btn-add-tier').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var bodyId = this.dataset.target;
            var body   = document.getElementById(bodyId);
            var row    = buildTierRow(null);
            body.appendChild(row);
            attachRowEvents(body.lastElementChild, bodyId);
        });
    });

    // ── Edit modal ────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-edit');
        if (!btn) return;
        var d = btn.dataset;

        document.getElementById('formEdit').action = '/admin/pdam-bills/' + d.id;
        document.getElementById('e_period_label').textContent =
            (monthNames[parseInt(d.month)] || d.month) + ' ' + d.year;

        // populate tiers
        var body = document.getElementById('tierBodyEdit');
        body.innerHTML = '';
        var tiers = [];
        try { tiers = JSON.parse(d.tiers || '[]'); } catch(ex) {}
        if (tiers.length === 0) tiers = [{}];
        tiers.forEach(function (t) {
            var row = buildTierRow(t);
            body.appendChild(row);
            attachRowEvents(body.lastElementChild, 'tierBodyEdit');
        });

        document.getElementById('e_add_display').value = parseInt(d.additional || 0).toLocaleString('id-ID');
        document.getElementById('e_add').value = d.additional || 0;
        document.getElementById('e_total_display').value = parseInt(d.total || 0).toLocaleString('id-ID');
        document.getElementById('e_total').value = d.total || '';
        document.getElementById('e_billdate').value = d.billdate;
        document.getElementById('e_duedate').value  = d.duedate;
        document.getElementById('e_invoice').value  = d.invoice || '';
        document.getElementById('e_notes').value    = d.notes || '';

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    });

    // ── Tambah modal: reset + add 1 default row ───────────────────
    document.getElementById('modalTambah').addEventListener('show.bs.modal', function () {
        var body = document.getElementById('tierBodyTambah');
        body.innerHTML = '';
        var row = buildTierRow(null);
        body.appendChild(row);
        attachRowEvents(body.lastElementChild, 'tierBodyTambah');
        document.getElementById('t_add_display').value = '';
        document.getElementById('t_add').value = '0';
        document.getElementById('t_total_display').value = '';
        document.getElementById('t_total').value = '';
    });

    // ── Konfirmasi ────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-konfirmasi');
        if (!btn) return;
        document.getElementById('konfirmasiLabel').textContent  = btn.dataset.label;
        document.getElementById('konfirmasiAmount').textContent = 'Rp ' + btn.dataset.amount;
        document.getElementById('formKonfirmasi').action = '/admin/pdam-bills/' + btn.dataset.id + '/confirm';
        new bootstrap.Modal(document.getElementById('modalKonfirmasi')).show();
    });

    // ── Delete ────────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/pdam-bills/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });

    // ── Search ────────────────────────────────────────────────────
    document.getElementById('billSearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#billTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    if (typeof feather !== 'undefined') feather.replace();
}());
</script>
@endsection
