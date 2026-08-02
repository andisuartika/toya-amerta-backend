@extends('layouts.vertical', ['title' => 'Pelanggan'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Data Pelanggan</h4>
            <p class="text-muted mb-0 fs-13">Kelola data pelanggan PDAM</p>
        </div>
        <button class="btn btn-primary btn-sm px-3" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalCustomer">
            <i data-feather="plus" style="width:15px;height:15px;" class="me-1"></i>Tambah Pelanggan
        </button>
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-4">
                    <label class="form-label mb-1 fs-13">Zona</label>
                    <select name="zone_id" class="form-select form-select-sm">
                        <option value="">Semua Zona</option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label mb-1 fs-13">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-4"><i data-feather="filter" style="width:13px;height:13px;" class="me-1"></i>Filter</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm px-4 ms-1"><i data-feather="x" style="width:13px;height:13px;" class="me-1"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="customersSearch" class="form-control form-control-sm w-auto"
                               placeholder="Cari pelanggan..." style="min-width:220px">
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="customersTable">
                            <thead>
                                <tr>
                                    <th>No. Pelanggan</th>
                                    <th>Nama</th>
                                    <th>Zona</th>
                                    <th>Tarif</th>
                                    <th>Alamat</th>
                                    <th class="text-end" style="width:100px">Meter Awal</th>
                                    <th class="text-center" style="width:90px">Status</th>
                                    <th class="text-end pe-3" style="width:120px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $customer)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fw-semibold font-monospace">
                                            {{ $customer->customer_number }}
                                        </span>
                                    </td>
                                    <td class="fw-medium">{{ $customer->name }}</td>
                                    <td>{{ $customer->zone->name ?? '—' }}</td>
                                    <td>{{ $customer->tariffRate->name ?? '—' }}</td>
                                    <td class="text-muted" style="max-width:200px">
                                        <span class="d-inline-block text-truncate" style="max-width:180px" title="{{ $customer->address }}">
                                            {{ $customer->address ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="text-end text-muted">{{ number_format($customer->initial_meter, 1) }} m³</td>
                                    <td class="text-center">
                                        @if ($customer->is_active)
                                            <span class="badge bg-success-subtle text-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a class="btn btn-sm bg-primary-subtle" role="button"
                                                data-bs-toggle="modal" data-bs-target="#modalCustomer"
                                                data-id="{{ $customer->id }}"
                                                data-customer_number="{{ $customer->customer_number }}"
                                                data-name="{{ $customer->name }}"
                                                data-zone_id="{{ $customer->zone_id }}"
                                                data-tariff_rate_id="{{ $customer->tariff_rate_id }}"
                                                data-address="{{ $customer->address }}"
                                                data-phone="{{ $customer->phone }}"
                                                data-installation_date="{{ $customer->installation_date?->format('Y-m-d') }}"
                                                data-initial_meter="{{ $customer->initial_meter }}"
                                                data-active="{{ $customer->is_active ? '1' : '0' }}"
                                                title="Edit">
                                                <i data-feather="edit-2" style="width:14px;height:14px;" class="text-primary"></i>
                                            </a>
                                            <a class="btn btn-sm bg-warning-subtle btn-replace-meter" role="button"
                                                data-bs-toggle="modal" data-bs-target="#modalReplaceMeter"
                                                data-id="{{ $customer->id }}"
                                                data-name="{{ $customer->name }}"
                                                title="Ganti Meteran">
                                                <i data-feather="refresh-cw" style="width:14px;height:14px;" class="text-warning"></i>
                                            </a>
                                            <a class="btn btn-sm bg-danger-subtle btn-delete" role="button"
                                                data-id="{{ $customer->id }}"
                                                data-name="{{ $customer->name }}"
                                                title="Hapus">
                                                <i data-feather="trash-2" style="width:14px;height:14px;" class="text-danger"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada data pelanggan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Form --}}
<div class="modal fade" id="modalCustomer" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formCustomer" method="POST">
                @csrf
                <div id="customerMethod"></div>
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="modalCustomerTitle">Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger py-2 fs-13 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">No. Pelanggan</label>
                            <div class="input-group">
                                <input type="text" name="customer_number" id="customerNumber"
                                       class="form-control font-monospace" placeholder="Otomatis">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnGenNumber">
                                    <i data-feather="refresh-cw" style="width:14px;height:14px;"></i>
                                </button>
                            </div>
                            <div class="form-text">Kosongkan untuk generate otomatis.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="customerName" class="form-control" required
                                   placeholder="Nama lengkap pelanggan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Zona <span class="text-danger">*</span></label>
                            <select name="zone_id" id="customerZone" class="form-select" required>
                                <option value="">-- Pilih Zona --</option>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Tarif <span class="text-danger">*</span></label>
                            <select name="tariff_rate_id" id="customerTariff" class="form-select" required>
                                <option value="">-- Pilih Tarif --</option>
                                @foreach ($tariffs as $tariff)
                                    <option value="{{ $tariff->id }}">{{ $tariff->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">No. HP</label>
                            <input type="text" name="phone" id="customerPhone" class="form-control"
                                   placeholder="cth: 081234567890">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Tanggal Pasang</label>
                            <input type="date" name="installation_date" id="customerInstallDate" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Meteran Awal (m³)</label>
                            <div class="input-group">
                                <input type="text" id="customerInitialMeterDisplay" class="form-control input-ribuan"
                                       inputmode="decimal" autocomplete="off" placeholder="0" data-target="#customerInitialMeter">
                                <input type="hidden" name="initial_meter" id="customerInitialMeter" value="0">
                                <span class="input-group-text">m³</span>
                            </div>
                            <div class="form-text">Angka meter saat pertama kali dipasang.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Alamat</label>
                            <textarea name="address" id="customerAddress" class="form-control" rows="2"
                                      placeholder="Alamat lengkap pelanggan"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                       id="customerActive" value="1" checked>
                                <label class="form-check-label" for="customerActive">Pelanggan Aktif</label>
                            </div>
                        </div>
                    </div>

                    {{-- Biaya Pemasangan (hanya saat tambah) --}}
                    <div id="sectionBiayaPasang" class="mt-3 pt-3 border-top">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="enableFee" value="1">
                                <label class="form-check-label fw-medium" for="enableFee">
                                    Catat Biaya Pemasangan
                                </label>
                            </div>
                            <span class="fs-12 text-muted">Opsional — bisa dicatat nanti</span>
                        </div>
                        <div id="feeFields" style="display:none">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Jenis Biaya <span class="text-danger">*</span></label>
                                    <select name="fee_type" id="feeType" class="form-select">
                                        <option value="biaya_pendaftaran">Biaya Pendaftaran</option>
                                        <option value="biaya_instalasi" selected>Biaya Instalasi</option>
                                        <option value="biaya_meteran">Biaya Meteran</option>
                                        <option value="uang_jaminan">Uang Jaminan</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Jumlah <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="feeAmountDisplay" class="form-control"
                                               inputmode="numeric" autocomplete="off" placeholder="0">
                                        <input type="hidden" name="fee_amount" id="feeAmount">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Tanggal Bayar <span class="text-danger">*</span></label>
                                    <input type="date" name="fee_payment_date" id="feePaymentDate"
                                           class="form-control" value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Metode</label>
                                    <select name="fee_payment_method" class="form-select">
                                        <option value="tunai" selected>Tunai (Cash)</option>
                                        <option value="transfer">Transfer Bank</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Keterangan</label>
                                    <input type="text" name="fee_description" class="form-control"
                                           placeholder="Opsional">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.partials.modal-delete', [
    'entity'  => 'Pelanggan',
    'warning' => 'Data tagihan pelanggan yang sudah ada tidak akan terhapus.',
])

{{-- Modal Ganti Meteran --}}
<div class="modal fade" id="modalReplaceMeter" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formReplaceMeter">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fs-15">Ganti Meteran — <span id="rm_customer_name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning bg-warning-subtle border-0 fs-13 py-2 px-3 mb-3">
                        Gunakan ini jika meteran fisik pelanggan diganti (rusak/error). Pencatatan periode
                        berikutnya akan memakai angka meteran baru sebagai acuan awal, bukan melanjutkan dari
                        meteran lama.
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Angka Akhir Meteran Lama</label>
                            <input type="text" class="form-control bg-light" id="rm_old_reading" readonly>
                            <div class="form-text">Diambil otomatis dari pencatatan/penggantian terakhir.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Angka Awal Meteran Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" id="rm_new_reading_display" class="form-control input-ribuan"
                                       inputmode="decimal" autocomplete="off" required placeholder="0" data-target="#rm_new_reading">
                                <input type="hidden" name="new_reading_start" id="rm_new_reading" value="0">
                                <span class="input-group-text">m³</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Tanggal Penggantian <span class="text-danger">*</span></label>
                            <input type="date" name="replaced_at" id="rm_replaced_at" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Alasan</label>
                            <input type="text" name="reason" class="form-control" placeholder="cth: Meteran rusak / macet">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Catatan</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Opsional"></textarea>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fs-13 fw-semibold text-muted mb-2">Riwayat Penggantian</h6>
                    <div id="rm_history" class="fs-13 text-muted">Memuat...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4">Simpan Penggantian</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script-bottom')
<script>
(function () {
    document.getElementById('customersSearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#customersTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    document.getElementById('btnGenNumber')?.addEventListener('click', function () {
        fetch('{{ route('admin.customers.generate-number') }}')
            .then(r => r.json())
            .then(d => { document.getElementById('customerNumber').value = d.number; });
    });

    // Format ribuan untuk biaya pemasangan
    function formatRibuan(val) {
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    document.getElementById('feeAmountDisplay')?.addEventListener('input', function () {
        var raw    = this.value.replace(/[^\d]/g, '');
        var num    = parseInt(raw, 10) || 0;
        var cursor = this.selectionStart, oldLen = this.value.length;
        this.value = raw ? formatRibuan(num) : '';
        document.getElementById('feeAmount').value = num > 0 ? num : '';
        var newLen = this.value.length;
        this.setSelectionRange(cursor + (newLen - oldLen), cursor + (newLen - oldLen));
    });

    // Toggle biaya pemasangan
    document.getElementById('enableFee')?.addEventListener('change', function () {
        document.getElementById('feeFields').style.display = this.checked ? '' : 'none';
    });

    document.getElementById('btnTambah').addEventListener('click', function () {
        document.getElementById('modalCustomerTitle').textContent = 'Tambah Pelanggan';
        document.getElementById('formCustomer').action = '{{ route('admin.customers.store') }}';
        document.getElementById('customerMethod').innerHTML = '';
        document.getElementById('formCustomer').reset();
        window.setRibuanValue(document.getElementById('customerInitialMeterDisplay'), '0');
        document.getElementById('customerActive').checked = true;
        document.getElementById('enableFee').checked = false;
        document.getElementById('feeFields').style.display = 'none';
        document.getElementById('sectionBiayaPasang').style.display = '';
        document.getElementById('feePaymentDate').value = '{{ now()->format('Y-m-d') }}';
        setTimeout(function () { feather.replace(); }, 50);
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-bs-target="#modalCustomer"][data-id]');
        if (!btn) return;
        var d = btn.dataset;
        document.getElementById('modalCustomerTitle').textContent = 'Edit Pelanggan';
        document.getElementById('formCustomer').action = '/admin/customers/' + d.id;
        document.getElementById('customerMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        // Sembunyikan section biaya pasang saat edit
        document.getElementById('sectionBiayaPasang').style.display = 'none';
        document.getElementById('customerNumber').value      = d.customer_number;
        document.getElementById('customerName').value        = d.name;
        document.getElementById('customerZone').value        = d.zone_id;
        document.getElementById('customerTariff').value      = d.tariff_rate_id;
        document.getElementById('customerPhone').value       = d.phone || '';
        document.getElementById('customerInstallDate').value  = d.installation_date || '';
        window.setRibuanValue(document.getElementById('customerInitialMeterDisplay'), d.initial_meter || '0');
        document.getElementById('customerAddress').value      = d.address || '';
        document.getElementById('customerActive').checked     = d.active === '1';
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/customers/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });

    // ── Ganti Meteran ──────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-replace-meter');
        if (!btn) return;
        var id = btn.dataset.id;

        document.getElementById('rm_customer_name').textContent = btn.dataset.name;
        document.getElementById('formReplaceMeter').action = '/admin/customers/' + id + '/replace-meter';
        document.getElementById('formReplaceMeter').reset();
        document.getElementById('rm_replaced_at').value = '{{ now()->format('Y-m-d') }}';
        window.setRibuanValue(document.getElementById('rm_new_reading_display'), '0');
        document.getElementById('rm_old_reading').value = 'Memuat...';
        document.getElementById('rm_history').textContent = 'Memuat...';

        fetch('/admin/customers/' + id + '/meter-info')
            .then(r => r.json())
            .then(function (d) {
                document.getElementById('rm_old_reading').value =
                    Number(d.current_reading).toLocaleString('id-ID', { minimumFractionDigits: 2 }) + ' m³';

                if (!d.history.length) {
                    document.getElementById('rm_history').textContent = 'Belum pernah diganti.';
                    return;
                }
                var html = '<ul class="list-unstyled mb-0">';
                d.history.forEach(function (h) {
                    html += '<li class="mb-1 pb-1 border-bottom">' +
                        '<strong>' + h.replaced_at + '</strong> — ' +
                        Number(h.old_reading_final).toLocaleString('id-ID', { minimumFractionDigits: 2 }) + ' m³ → ' +
                        Number(h.new_reading_start).toLocaleString('id-ID', { minimumFractionDigits: 2 }) + ' m³' +
                        (h.reason ? ' <span class="text-muted">(' + h.reason + ')</span>' : '') +
                        '</li>';
                });
                html += '</ul>';
                document.getElementById('rm_history').innerHTML = html;
            });
    });

}());

@if ($errors->any() && !old('_method'))
document.addEventListener('DOMContentLoaded', function () {
    function formatRibuan(val) {
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    document.getElementById('modalCustomerTitle').textContent = 'Tambah Pelanggan';
    document.getElementById('formCustomer').action = '{{ route('admin.customers.store') }}';
    document.getElementById('customerMethod').innerHTML = '';
    document.getElementById('customerNumber').value       = '{{ old('customer_number') }}';
    document.getElementById('customerName').value         = '{{ old('name') }}';
    document.getElementById('customerZone').value         = '{{ old('zone_id') }}';
    document.getElementById('customerTariff').value       = '{{ old('tariff_rate_id') }}';
    document.getElementById('customerPhone').value        = '{{ old('phone') }}';
    document.getElementById('customerInstallDate').value  = '{{ old('installation_date') }}';
    window.setRibuanValue(document.getElementById('customerInitialMeterDisplay'), '{{ old('initial_meter', '0') }}');
    document.getElementById('customerAddress').value      = '{{ addslashes(old('address', '')) }}';
    document.getElementById('customerActive').checked     = {{ old('is_active') ? 'true' : 'false' }};
    document.getElementById('sectionBiayaPasang').style.display = '';
    @if (old('fee_amount'))
    document.getElementById('enableFee').checked      = true;
    document.getElementById('feeFields').style.display = '';
    document.getElementById('feeAmountDisplay').value  = formatRibuan({{ (int) old('fee_amount', 0) }});
    document.getElementById('feeAmount').value         = '{{ old('fee_amount') }}';
    document.getElementById('feeType').value           = '{{ old('fee_type', 'biaya_instalasi') }}';
    document.getElementById('feePaymentDate').value    = '{{ old('fee_payment_date') }}';
    @endif
    new bootstrap.Modal(document.getElementById('modalCustomer')).show();
});
@endif
</script>
@endsection
