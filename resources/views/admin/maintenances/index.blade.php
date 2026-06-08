@extends('layouts.vertical', ['title' => 'Maintenance'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Maintenance</h4>
            <p class="text-muted mb-0 fs-13">Kelola laporan kerusakan dan perbaikan jaringan air</p>
        </div>
        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i data-feather="plus" style="width:15px;height:15px;" class="me-1"></i>Tambah Laporan
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 fs-13">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-sm-3">
            <div class="card border-0 bg-danger-subtle">
                <div class="card-body py-3 text-center">
                    <div class="fs-22 fw-bold text-danger">{{ $summary['dilaporkan'] }}</div>
                    <div class="fs-12 text-muted">Dilaporkan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card border-0 bg-warning-subtle">
                <div class="card-body py-3 text-center">
                    <div class="fs-22 fw-bold text-warning">{{ $summary['dalam_proses'] }}</div>
                    <div class="fs-12 text-muted">Dalam Proses</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card border-0 bg-secondary-subtle">
                <div class="card-body py-3 text-center">
                    <div class="fs-22 fw-bold text-secondary">{{ $summary['ditunda'] }}</div>
                    <div class="fs-12 text-muted">Ditunda</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card border-0 bg-success-subtle">
                <div class="card-body py-3 text-center">
                    <div class="fs-22 fw-bold text-success">{{ $summary['selesai'] }}</div>
                    <div class="fs-12 text-muted">Selesai</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label mb-1 fs-13">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="dilaporkan"   {{ ($filters['status'] ?? '') === 'dilaporkan'   ? 'selected' : '' }}>Dilaporkan</option>
                        <option value="dalam_proses" {{ ($filters['status'] ?? '') === 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
                        <option value="ditunda"      {{ ($filters['status'] ?? '') === 'ditunda'      ? 'selected' : '' }}>Ditunda</option>
                        <option value="selesai"      {{ ($filters['status'] ?? '') === 'selesai'      ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Prioritas</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="darurat" {{ ($filters['priority'] ?? '') === 'darurat' ? 'selected' : '' }}>Darurat</option>
                        <option value="tinggi"  {{ ($filters['priority'] ?? '') === 'tinggi'  ? 'selected' : '' }}>Tinggi</option>
                        <option value="sedang"  {{ ($filters['priority'] ?? '') === 'sedang'  ? 'selected' : '' }}>Sedang</option>
                        <option value="rendah"  {{ ($filters['priority'] ?? '') === 'rendah'  ? 'selected' : '' }}>Rendah</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Kategori</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="pipa_bocor"     {{ ($filters['category'] ?? '') === 'pipa_bocor'     ? 'selected' : '' }}>Pipa Bocor</option>
                        <option value="meteran_rusak"  {{ ($filters['category'] ?? '') === 'meteran_rusak'  ? 'selected' : '' }}>Meteran Rusak</option>
                        <option value="pompa"          {{ ($filters['category'] ?? '') === 'pompa'          ? 'selected' : '' }}>Pompa</option>
                        <option value="reservoir"      {{ ($filters['category'] ?? '') === 'reservoir'      ? 'selected' : '' }}>Reservoir</option>
                        <option value="instalasi_baru" {{ ($filters['category'] ?? '') === 'instalasi_baru' ? 'selected' : '' }}>Instalasi Baru</option>
                        <option value="lainnya"        {{ ($filters['category'] ?? '') === 'lainnya'        ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Zona</label>
                    <select name="zone_id" class="form-select form-select-sm">
                        <option value="">Semua Zona</option>
                        @foreach ($zones as $z)
                            <option value="{{ $z->id }}" {{ ($filters['zone_id'] ?? '') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i data-feather="filter" style="width:13px;height:13px;" class="me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.maintenances.index') }}" class="btn btn-outline-secondary btn-sm px-3 ms-1">
                        <i data-feather="x" style="width:13px;height:13px;" class="me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fs-15">Daftar Laporan Maintenance</h5>
            <input type="text" id="maintenanceSearch" class="form-control form-control-sm"
                   placeholder="Cari..." style="width:200px">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="maintenanceTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:35%">Judul / Lokasi</th>
                            <th>Kategori</th>
                            <th class="text-center">Prioritas</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Biaya</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-end pe-3" style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($maintenances as $m)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-medium">{{ $m->title }}</div>
                                <div class="fs-12 text-muted">
                                    <i data-feather="map-pin" style="width:11px;height:11px;"></i>
                                    {{ $m->location }}
                                    @if ($m->zone) · {{ $m->zone->name }} @endif
                                </div>
                                @if ($m->customer)
                                    <div class="fs-11 text-muted">Pelanggan: {{ $m->customer->name }}</div>
                                @endif
                            </td>
                            <td class="fs-13">{{ \App\Models\Maintenance::categoryLabel($m->category) }}</td>
                            <td class="text-center">
                                @php
                                    $pBadge = match($m->priority) {
                                        'darurat' => 'bg-danger text-white',
                                        'tinggi'  => 'bg-warning-subtle text-warning',
                                        'sedang'  => 'bg-info-subtle text-info',
                                        default   => 'bg-secondary-subtle text-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $pBadge }}">{{ \App\Models\Maintenance::priorityLabel($m->priority) }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $sBadge = match($m->status) {
                                        'dilaporkan'   => 'bg-danger-subtle text-danger',
                                        'dalam_proses' => 'bg-warning-subtle text-warning',
                                        'selesai'      => 'bg-success-subtle text-success',
                                        default        => 'bg-secondary-subtle text-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $sBadge }}">{{ \App\Models\Maintenance::statusLabel($m->status) }}</span>
                            </td>
                            <td class="text-end fs-13">
                                @if ($m->total_cost > 0)
                                    Rp {{ number_format($m->total_cost, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center fs-12 text-muted">
                                {{ $m->reported_date->format('d/m/Y') }}
                                @if ($m->completed_date)
                                    <div class="text-success">✓ {{ $m->completed_date->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                {{-- Update Status --}}
                                @if ($m->status !== 'selesai')
                                <button class="btn btn-sm bg-warning-subtle me-1 btn-status"
                                    data-id="{{ $m->id }}"
                                    data-title="{{ $m->title }}"
                                    data-status="{{ $m->status }}"
                                    data-material="{{ $m->material_cost }}"
                                    data-labor="{{ $m->labor_cost }}"
                                    title="Update Status">
                                    <i data-feather="refresh-cw" style="width:13px;height:13px;" class="text-warning"></i>
                                </button>
                                @endif
                                {{-- Edit --}}
                                <button class="btn btn-sm bg-primary-subtle me-1 btn-edit"
                                    data-id="{{ $m->id }}"
                                    data-title="{{ $m->title }}"
                                    data-location="{{ $m->location }}"
                                    data-category="{{ $m->category }}"
                                    data-priority="{{ $m->priority }}"
                                    data-reported_date="{{ $m->reported_date->format('Y-m-d') }}"
                                    data-zone_id="{{ $m->zone_id }}"
                                    data-customer_id="{{ $m->customer_id }}"
                                    data-description="{{ $m->description }}"
                                    data-material="{{ $m->material_cost }}"
                                    data-labor="{{ $m->labor_cost }}"
                                    title="Edit">
                                    <i data-feather="edit-2" style="width:13px;height:13px;" class="text-primary"></i>
                                </button>
                                {{-- Hapus --}}
                                <button class="btn btn-sm bg-danger-subtle btn-delete"
                                    data-id="{{ $m->id }}"
                                    data-name="{{ $m->title }}"
                                    title="Hapus">
                                    <i data-feather="trash-2" style="width:13px;height:13px;" class="text-danger"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i data-feather="tool" style="width:32px;height:32px;" class="mb-2 opacity-30 d-block mx-auto"></i>
                                Belum ada laporan maintenance.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.maintenances.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Tambah Laporan Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.maintenances._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formEdit">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Edit Laporan Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.maintenances._form', ['edit' => true])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Update Status --}}
<div class="modal fade" id="modalStatus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formStatus">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Update Status Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted fs-13 mb-3" id="statusTitle"></p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                        <select name="status" id="statusSelect" class="form-select" required>
                            <option value="dilaporkan">Dilaporkan</option>
                            <option value="dalam_proses">Dalam Proses</option>
                            <option value="ditunda">Ditunda</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="row g-3" id="biayaSection">
                        <div class="col-6">
                            <label class="form-label fw-medium">Biaya Material</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="statusMaterialDisplay"
                                       class="form-control input-ribuan" inputmode="numeric"
                                       autocomplete="off" placeholder="0" data-target="material_cost">
                                <input type="hidden" name="material_cost" id="statusMaterial">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium">Biaya Tenaga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="statusLaborDisplay"
                                       class="form-control input-ribuan" inputmode="numeric"
                                       autocomplete="off" placeholder="0" data-target="labor_cost">
                                <input type="hidden" name="labor_cost" id="statusLabor">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-medium">Catatan</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Opsional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.partials.modal-delete', [
    'entity'  => 'Laporan Maintenance',
    'warning' => 'Data maintenance yang dihapus tidak dapat dikembalikan.',
])
@endsection

@section('script-bottom')
<script>
(function () {
    // Format ribuan
    function formatRibuan(val) {
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    document.querySelectorAll('.input-ribuan').forEach(function (el) {
        el.addEventListener('input', function () {
            var raw    = this.value.replace(/[^\d]/g, '');
            var num    = parseInt(raw, 10) || 0;
            var cursor = this.selectionStart;
            var oldLen = this.value.length;

            this.value = raw ? formatRibuan(num) : '';

            // Sync ke hidden field
            var targetName = this.dataset.target;
            var hidden = this.closest('form')
                ? this.closest('form').querySelector('[name="' + targetName + '"][type="hidden"]')
                : document.getElementById('status' + (targetName === 'material_cost' ? 'Material' : 'Labor'));
            if (hidden) hidden.value = num || '';

            var newLen = this.value.length;
            this.setSelectionRange(cursor + (newLen - oldLen), cursor + (newLen - oldLen));
        });
    });

    // Search
    document.getElementById('maintenanceSearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#maintenanceTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    // Edit modal
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-edit');
        if (!btn) return;
        var d = btn.dataset;
        var form = document.getElementById('formEdit');
        form.action = '/admin/maintenances/' + d.id;
        form.querySelector('[name="title"]').value         = d.title;
        form.querySelector('[name="location"]').value      = d.location;
        form.querySelector('[name="category"]').value      = d.category;
        form.querySelector('[name="priority"]').value      = d.priority;
        form.querySelector('[name="reported_date"]').value = d.reported_date;
        form.querySelector('[name="zone_id"]').value       = d.zone_id || '';
        form.querySelector('[name="description"]').value   = d.description || '';
        form.querySelector('[name="material_cost"]').value = d.material || '';
        form.querySelector('[name="labor_cost"]').value    = d.labor || '';
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    });

    // Status modal
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-status');
        if (!btn) return;
        var d = btn.dataset;
        var form = document.getElementById('formStatus');
        form.action = '/admin/maintenances/' + d.id + '/status';
        document.getElementById('statusTitle').textContent = d.title;
        document.getElementById('statusSelect').value = d.status;

        var mat = parseFloat(d.material) || 0;
        var lab = parseFloat(d.labor) || 0;
        document.getElementById('statusMaterialDisplay').value = mat ? formatRibuan(mat) : '';
        document.getElementById('statusLaborDisplay').value    = lab ? formatRibuan(lab) : '';
        document.getElementById('statusMaterial').value        = mat || '';
        document.getElementById('statusLabor').value           = lab || '';

        new bootstrap.Modal(document.getElementById('modalStatus')).show();
    });

    // Delete modal
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/maintenances/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });
}());
</script>
@endsection
