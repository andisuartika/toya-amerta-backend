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
                    <button type="submit" class="btn btn-soft-primary btn-sm px-3">Filter</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-soft-secondary btn-sm px-3 ms-1">Reset</a>
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
                               placeholder="&#x1F50D; Cari pelanggan..." style="min-width:220px">
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
                                    <th class="text-end" style="width:100px">Aksi</th>
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
                                        <a class="btn btn-sm bg-primary-subtle me-1" role="button"
                                            data-bs-toggle="modal" data-bs-target="#modalCustomer"
                                            data-id="{{ $customer->id }}"
                                            data-customer_number="{{ $customer->customer_number }}"
                                            data-name="{{ $customer->name }}"
                                            data-zone_id="{{ $customer->zone_id }}"
                                            data-tariff_rate_id="{{ $customer->tariff_rate_id }}"
                                            data-address="{{ $customer->address }}"
                                            data-phone="{{ $customer->phone }}"
                                            data-installation_date="{{ $customer->installation_date }}"
                                            data-initial_meter="{{ $customer->initial_meter }}"
                                            data-active="{{ $customer->is_active ? '1' : '0' }}"
                                            title="Edit">
                                            <i data-feather="edit-2" style="width:14px;height:14px;" class="text-primary"></i>
                                        </a>
                                        <a class="btn btn-sm bg-danger-subtle btn-delete" role="button"
                                            data-id="{{ $customer->id }}"
                                            data-name="{{ $customer->name }}"
                                            title="Hapus">
                                            <i data-feather="trash-2" style="width:14px;height:14px;" class="text-danger"></i>
                                        </a>
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
                                <input type="number" name="initial_meter" id="customerInitialMeter"
                                       class="form-control" min="0" step="0.01" value="0" placeholder="0">
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

    document.getElementById('btnTambah').addEventListener('click', function () {
        document.getElementById('modalCustomerTitle').textContent = 'Tambah Pelanggan';
        document.getElementById('formCustomer').action = '{{ route('admin.customers.store') }}';
        document.getElementById('customerMethod').innerHTML = '';
        document.getElementById('formCustomer').reset();
        document.getElementById('customerActive').checked = true;
        setTimeout(function () { feather.replace(); }, 50);
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-bs-target="#modalCustomer"][data-id]');
        if (!btn) return;
        var d = btn.dataset;
        document.getElementById('modalCustomerTitle').textContent = 'Edit Pelanggan';
        document.getElementById('formCustomer').action = '/admin/customers/' + d.id;
        document.getElementById('customerMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('customerNumber').value      = d.customer_number;
        document.getElementById('customerName').value        = d.name;
        document.getElementById('customerZone').value        = d.zone_id;
        document.getElementById('customerTariff').value      = d.tariff_rate_id;
        document.getElementById('customerPhone').value       = d.phone || '';
        document.getElementById('customerInstallDate').value  = d.installation_date || '';
        document.getElementById('customerInitialMeter').value = d.initial_meter || '0';
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
}());
</script>
@endsection
