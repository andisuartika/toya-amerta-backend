@extends('layouts.vertical', ['title' => 'Tarif Air'])


@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Tarif Air</h4>
            <p class="text-muted mb-0 fs-13">Kelola tarif dan harga pemakaian air</p>
        </div>
        <button class="btn btn-primary btn-sm px-3" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalTariff">
            <i data-feather="plus" style="width:15px;height:15px;" class="me-1"></i>Tambah Tarif
        </button>
    </div>

    <div class="alert alert-info bg-info-subtle border-0 d-flex gap-2 align-items-start py-2 px-3 fs-13 mb-3">
        <iconify-icon icon="solar:info-circle-bold-duotone" class="fs-18 text-info flex-shrink-0 mt-1"></iconify-icon>
        <span>
            <strong>Formula:</strong>
            Pemakaian efektif = MAX(usage, min. pemakaian) →
            Biaya = efektif × harga/m³ →
            Tagihan = MAX(biaya, min. tagihan)
        </span>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="tariffSearch" class="form-control form-control-sm w-auto"
                               placeholder="&#x1F50D; Cari tarif..." style="min-width:220px">
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="tariffTable">
                            <thead>
                                <tr>
                                    <th>Nama Tarif</th>
                                    <th class="text-end">Harga / m³</th>
                                    <th class="text-end">Min. Tagihan</th>
                                    <th class="text-end">Min. Pemakaian</th>
                                    <th class="text-center" style="width:90px">Status</th>
                                    <th class="text-end" style="width:100px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tariffs as $tariff)
                                <tr>
                                    <td class="fw-medium">{{ $tariff->name }}</td>
                                    <td class="text-end">Rp {{ number_format($tariff->price_per_m3, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($tariff->minimum_charge, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ $tariff->minimum_usage }} m³</td>
                                    <td class="text-center">
                                        @if ($tariff->is_active)
                                            <span class="badge bg-success-subtle text-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm bg-primary-subtle me-1" role="button"
                                            data-bs-toggle="modal" data-bs-target="#modalTariff"
                                            data-id="{{ $tariff->id }}"
                                            data-name="{{ $tariff->name }}"
                                            data-price="{{ $tariff->price_per_m3 }}"
                                            data-mincharge="{{ $tariff->minimum_charge }}"
                                            data-minusage="{{ $tariff->minimum_usage }}"
                                            data-active="{{ $tariff->is_active ? '1' : '0' }}"
                                            title="Edit">
                                            <i data-feather="edit-2" style="width:14px;height:14px;" class="text-primary"></i>
                                        </a>
                                        <a class="btn btn-sm bg-danger-subtle btn-delete" role="button"
                                            data-id="{{ $tariff->id }}"
                                            data-name="{{ $tariff->name }}"
                                            title="Hapus">
                                            <i data-feather="trash-2" style="width:14px;height:14px;" class="text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada data tarif</td>
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
<div class="modal fade" id="modalTariff" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formTariff" method="POST">
                @csrf
                <div id="tariffMethod"></div>
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="modalTariffTitle">Tambah Tarif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Tarif <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="tariffName" class="form-control"
                               required placeholder="cth: Tarif Rumah Tangga">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Harga per m³ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price_per_m3" id="tariffPrice"
                                       class="form-control" required min="0" step="100" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Min. Tagihan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="minimum_charge" id="tariffMinCharge"
                                       class="form-control" required min="0" step="100" placeholder="0">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Min. Pemakaian <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="minimum_usage" id="tariffMinUsage"
                                       class="form-control" required min="0" step="0.5" value="1">
                                <span class="input-group-text">m³</span>
                            </div>
                            <div class="form-text">Pemakaian di bawah nilai ini dihitung sebesar minimum.</div>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="tariffActive" value="1" checked>
                        <label class="form-check-label" for="tariffActive">Tarif Aktif</label>
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
    'entity'  => 'Tarif',
    'warning' => 'Tarif yang masih digunakan pelanggan tidak dapat dihapus.',
])
@endsection

@section('script-bottom')
<script>
(function () {
    document.getElementById('tariffSearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#tariffTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    document.getElementById('btnTambah').addEventListener('click', function () {
        document.getElementById('modalTariffTitle').textContent = 'Tambah Tarif';
        document.getElementById('formTariff').action = '{{ route('admin.tariff-rates.store') }}';
        document.getElementById('tariffMethod').innerHTML = '';
        document.getElementById('formTariff').reset();
        document.getElementById('tariffMinUsage').value = '1';
        document.getElementById('tariffActive').checked = true;
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-bs-target="#modalTariff"][data-id]');
        if (!btn) return;
        var d = btn.dataset;
        document.getElementById('modalTariffTitle').textContent = 'Edit Tarif';
        document.getElementById('formTariff').action = '/admin/tariff-rates/' + d.id;
        document.getElementById('tariffMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('tariffName').value      = d.name;
        document.getElementById('tariffPrice').value     = d.price;
        document.getElementById('tariffMinCharge').value = d.mincharge;
        document.getElementById('tariffMinUsage').value  = d.minusage;
        document.getElementById('tariffActive').checked  = d.active === '1';
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/tariff-rates/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });
}());
</script>
@endsection
