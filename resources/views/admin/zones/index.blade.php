@extends('layouts.vertical', ['title' => 'Zona Wilayah'])


@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Zona Wilayah</h4>
            <p class="text-muted mb-0 fs-13">Kelola zona distribusi air</p>
        </div>
        <button class="btn btn-primary btn-sm px-3" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalZone">
            <i data-feather="plus" style="width:15px;height:15px;" class="me-1"></i>Tambah Zona
        </button>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="zonesSearch" class="form-control form-control-sm w-auto"
                               placeholder="Cari zona..." style="min-width:220px">
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="zonesTable">
                            <thead>
                                <tr>
                                    <th>Nama Zona</th>
                                    <th>Kode</th>
                                    <th>Keterangan</th>
                                    <th class="text-center" style="width:90px">Status</th>
                                    <th class="text-end" style="width:100px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($zones as $zone)
                                <tr>
                                    <td class="fw-medium">{{ $zone->name }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $zone->code }}</span>
                                    </td>
                                    <td class="text-muted">{{ $zone->description ?: '—' }}</td>
                                    <td class="text-center">
                                        @if ($zone->is_active)
                                            <span class="badge bg-success-subtle text-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm bg-primary-subtle me-1" role="button"
                                            data-bs-toggle="modal" data-bs-target="#modalZone"
                                            data-id="{{ $zone->id }}"
                                            data-name="{{ $zone->name }}"
                                            data-code="{{ $zone->code }}"
                                            data-description="{{ $zone->description }}"
                                            data-active="{{ $zone->is_active ? '1' : '0' }}"
                                            title="Edit">
                                            <i data-feather="edit-2" style="width:14px;height:14px;" class="text-primary"></i>
                                        </a>
                                        <a class="btn btn-sm bg-danger-subtle btn-delete" role="button"
                                            data-id="{{ $zone->id }}"
                                            data-name="{{ $zone->name }}"
                                            title="Hapus">
                                            <i data-feather="trash-2" style="width:14px;height:14px;" class="text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada data zona</td>
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

{{-- Modal Tambah / Edit --}}
<div class="modal fade" id="modalZone" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formZone" method="POST">
                @csrf
                <div id="zoneMethod"></div>
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="modalZoneTitle">Tambah Zona</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Zona <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="zoneName" class="form-control"
                               required placeholder="cth: Zona A – Desa Utara">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Kode Zona <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="zoneCode" class="form-control text-uppercase"
                               required placeholder="cth: ZA01" maxlength="20">
                        <div class="form-text">Kode unik, otomatis huruf kapital.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Keterangan</label>
                        <textarea name="description" id="zoneDesc" class="form-control" rows="2"
                                  placeholder="Deskripsi singkat zona (opsional)"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="zoneActive" value="1" checked>
                        <label class="form-check-label" for="zoneActive">Zona Aktif</label>
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
    'entity'  => 'Zona',
    'warning' => 'Zona yang memiliki pelanggan tidak dapat dihapus.',
])
@endsection

@section('script-bottom')
<script>
(function () {
    // Search filter
    document.getElementById('zonesSearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#zonesTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    // Tambah → reset form
    document.getElementById('btnTambah').addEventListener('click', function () {
        document.getElementById('modalZoneTitle').textContent = 'Tambah Zona';
        document.getElementById('formZone').action = '{{ route('admin.zones.store') }}';
        document.getElementById('zoneMethod').innerHTML = '';
        document.getElementById('formZone').reset();
        document.getElementById('zoneActive').checked = true;
    });

    // Edit — event delegation (karena simple-datatables mungkin merender ulang baris)
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-bs-target="#modalZone"][data-id]');
        if (!btn) return;
        var d = btn.dataset;
        document.getElementById('modalZoneTitle').textContent = 'Edit Zona';
        document.getElementById('formZone').action = '/admin/zones/' + d.id;
        document.getElementById('zoneMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('zoneName').value  = d.name;
        document.getElementById('zoneCode').value  = d.code;
        document.getElementById('zoneDesc').value  = d.description || '';
        document.getElementById('zoneActive').checked = d.active === '1';
    });

    // Delete
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/zones/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });

    document.getElementById('zoneCode')?.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });

    if (typeof feather !== 'undefined') feather.replace();
}());
</script>
@endsection
