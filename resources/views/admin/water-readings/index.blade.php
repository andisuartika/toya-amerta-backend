@extends('layouts.vertical', ['title' => 'Pencatatan Meteran'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Pencatatan Meteran</h4>
            <p class="text-muted mb-0 fs-13">Input pemakaian air bulanan pelanggan</p>
        </div>
        <a href="{{ route('admin.water-readings.history') }}" class="btn btn-soft-secondary btn-sm px-3">
            <i data-feather="clock" style="width:14px;height:14px;" class="me-1"></i>Riwayat Pembacaan
        </a>
    </div>

    {{-- Filter Periode --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label mb-1 fs-13">Bulan</label>
                    <select name="month" class="form-select form-select-sm">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Tahun</label>
                    <select name="year" class="form-select form-select-sm">
                        @foreach(range(now()->year, 2020) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-soft-primary btn-sm px-3">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">

        {{-- Form Input Meteran --}}
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 fs-15">
                        <i data-feather="edit-3" style="width:16px;height:16px;" class="me-1 text-primary"></i>
                        Input Meteran
                    </h5>
                </div>
                <div class="card-body">

                    @if (session('error'))
                        <div class="alert alert-danger py-2 fs-13">{{ session('error') }}</div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success py-2 fs-13">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.water-readings.store') }}" id="formReading">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium">Pelanggan <span class="text-danger">*</span></label>
                            <select name="customer_id" id="selectCustomer" class="form-select select2-customer" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach ($unrecorded as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->customer_number }} – {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                            <div class="form-text" id="customerMeta"></div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-medium">Bulan</label>
                                <select name="period_month" class="form-select" required>
                                    @foreach(range(1,12) as $m)
                                        <option value="{{ $m }}" {{ (old('period_month', $month) == $m) ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium">Tahun</label>
                                <select name="period_year" class="form-select" required>
                                    @foreach(range(now()->year, 2020) as $y)
                                        <option value="{{ $y }}" {{ (old('period_year', $year) == $y) ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Meter Sebelumnya</label>
                            <div class="input-group">
                                <input type="number" id="previousReading" class="form-control bg-light"
                                       readonly placeholder="Otomatis dari data terakhir" step="0.01">
                                <span class="input-group-text">m³</span>
                            </div>
                            <div class="form-text">Terisi otomatis dari pembacaan sebelumnya.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Meter Saat Ini <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="current_reading" id="currentReading"
                                       class="form-control" required step="0.01" min="0"
                                       placeholder="0.00" value="{{ old('current_reading') }}">
                                <span class="input-group-text">m³</span>
                            </div>
                            @error('current_reading')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Preview pemakaian --}}
                        <div id="usagePreview" class="alert alert-soft-info py-2 fs-13 mb-3" style="display:none">
                            Pemakaian: <strong id="previewUsage">0</strong> m³ →
                            Est. tagihan: <strong id="previewAmount">Rp 0</strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Tanggal Baca <span class="text-danger">*</span></label>
                            <input type="date" name="reading_date" class="form-control"
                                   value="{{ old('reading_date', now()->format('Y-m-d')) }}" required>
                            @error('reading_date')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Catatan</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Opsional">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i data-feather="save" style="width:15px;height:15px;" class="me-1"></i>Simpan Pencatatan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar Pencatatan Periode Ini --}}
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fs-15">
                        <i data-feather="list" style="width:16px;height:16px;" class="me-1 text-primary"></i>
                        Tercatat —
                        {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
                        <span class="badge bg-primary-subtle text-primary ms-1">{{ $readings->count() }}</span>
                    </h5>
                    <span class="text-muted fs-12">
                        Belum tercatat: <strong>{{ $unrecorded->count() }}</strong> pelanggan
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="readingsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Pelanggan</th>
                                    <th class="text-end">Meter Lalu</th>
                                    <th class="text-end">Meter Kini</th>
                                    <th class="text-end">Pakai (m³)</th>
                                    <th class="text-end">Tagihan</th>
                                    <th class="text-center" style="width:90px">Status</th>
                                    <th class="text-end pe-3" style="width:70px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($readings as $r)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-medium">{{ $r->customer->name }}</div>
                                        <div class="fs-12 text-muted">{{ $r->customer->customer_number }}</div>
                                    </td>
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
                                    <td class="text-end pe-3">
                                        @if ($r->payment_status === 'belum_bayar')
                                        <a class="btn btn-sm bg-danger-subtle btn-delete" role="button"
                                           data-id="{{ $r->id }}"
                                           data-name="{{ $r->customer->name }}"
                                           title="Hapus">
                                            <i data-feather="trash-2" style="width:13px;height:13px;" class="text-danger"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        Belum ada pencatatan untuk periode ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if ($readings->count())
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="ps-3 fw-semibold text-end">Total Tagihan:</td>
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
        </div>

    </div>
</div>

@include('admin.partials.modal-delete', [
    'entity'  => 'Pencatatan',
    'warning' => 'Pencatatan yang sudah lunas tidak dapat dihapus.',
])
@endsection

@section('script-bottom')
<script>
window.addEventListener('load', function () {
(function () {
    var customerInfoUrl = '{{ route('admin.water-readings.customer-info', ['customer' => '__ID__']) }}';

    var prevReading = 0;
    var tariffData  = null;

    // Init select2
    $('#selectCustomer').select2({
        placeholder: '-- Cari nama / nomor pelanggan --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () { return 'Pelanggan tidak ditemukan'; },
            searching: function () { return 'Mencari...'; },
        },
    });

    // Event pakai select2 change
    $('#selectCustomer').on('change', function () {
        var id = this.value;
        document.getElementById('customerMeta').textContent = '';
        document.getElementById('previousReading').value    = '';
        document.getElementById('usagePreview').style.display = 'none';
        prevReading = 0;
        tariffData  = null;

        if (!id) return;

        fetch(customerInfoUrl.replace('__ID__', id))
            .then(r => r.json())
            .then(function (d) {
                prevReading = parseFloat(d.previous_reading) || 0;
                document.getElementById('previousReading').value = prevReading.toFixed(2);
                document.getElementById('customerMeta').textContent =
                    'Zona: ' + d.zone_name + ' | Tarif: ' + d.tariff_name;
                tariffData = d;
                updatePreview();
            });
    });

    document.getElementById('currentReading')?.addEventListener('input', updatePreview);

    function updatePreview() {
        var cur = parseFloat(document.getElementById('currentReading').value) || 0;
        if (!tariffData || cur <= 0) {
            document.getElementById('usagePreview').style.display = 'none';
            return;
        }
        var usage    = Math.max(0, cur - prevReading);
        var billable = Math.max(usage, parseFloat(tariffData.minimum_usage || 1));
        var cost     = Math.max(billable * parseFloat(tariffData.price_per_m3 || 0),
                                parseFloat(tariffData.minimum_charge || 0));

        document.getElementById('previewUsage').textContent  = usage.toFixed(2);
        document.getElementById('previewAmount').textContent = 'Rp ' + cost.toLocaleString('id-ID');
        document.getElementById('usagePreview').style.display = '';
    }

    // Delete modal
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/water-readings/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });

    // Search tabel
    document.getElementById('readingsSearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#readingsTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}());
}); // end window.load
</script>
@endsection
