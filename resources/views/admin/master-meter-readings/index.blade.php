@extends('layouts.vertical', ['title' => 'Meteran Induk'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Pencatatan Meteran Induk</h4>
            <p class="text-muted mb-0 fs-13">Meteran sumber air sebelum disebarkan ke seluruh pelanggan — untuk memantau kebocoran distribusi.</p>
        </div>
        <button class="btn btn-primary btn-sm px-3 mt-2 mt-sm-0" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i data-feather="plus" style="width:14px;height:14px;" class="me-1"></i>Catat Meteran Induk
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 fs-13">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 fs-13">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fs-15">Riwayat Pencatatan (24 periode terakhir)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th class="text-end">Meter Lalu</th>
                            <th class="text-end">Meter Ini</th>
                            <th class="text-end">Disalurkan (m³)</th>
                            <th class="text-end">Terpakai Pelanggan (m³)</th>
                            <th class="text-end">Kebocoran</th>
                            <th>Petugas</th>
                            <th class="text-end" style="width:60px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($readings as $reading)
                        <tr>
                            <td class="fw-medium">{{ $reading->period_label }}</td>
                            <td class="text-end">{{ number_format($reading->previous_reading, 1) }}</td>
                            <td class="text-end">{{ number_format($reading->current_reading, 1) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($reading->usage_m3, 1) }}</td>
                            <td class="text-end">{{ number_format($reading->customer_usage, 1) }}</td>
                            <td class="text-end">
                                @if ($reading->loss_percent > 15)
                                    <span class="badge bg-danger-subtle text-danger">{{ $reading->loss_percent }}% ({{ number_format($reading->loss_m3, 1) }} m³)</span>
                                @elseif ($reading->loss_percent > 0)
                                    <span class="badge bg-warning-subtle text-warning">{{ $reading->loss_percent }}% ({{ number_format($reading->loss_m3, 1) }} m³)</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">0%</span>
                                @endif
                            </td>
                            <td class="text-muted fs-13">{{ $reading->recordedBy?->name ?? '—' }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm bg-danger-subtle btn-delete" role="button"
                                    data-id="{{ $reading->id }}"
                                    data-name="periode {{ $reading->period_label }}"
                                    title="Hapus">
                                    <i data-feather="trash-2" style="width:14px;height:14px;" class="text-danger"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada pencatatan meteran induk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="text-muted fs-12 mt-2">
        <i data-feather="info" style="width:12px;height:12px;"></i>
        "Kebocoran" dihitung dari selisih antara air yang disalurkan dari meteran induk dengan total pemakaian yang tercatat di seluruh meteran pelanggan pada periode yang sama (non-revenue water). Persentase di atas 15% perlu ditelusuri lebih lanjut.
    </p>

</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.master-meter-readings.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Catat Meteran Induk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-medium">Bulan <span class="text-danger">*</span></label>
                            <select name="period_month" class="form-select" required>
                                @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $m)
                                    <option value="{{ $i + 1 }}" @selected(($i + 1) == now()->month)>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium">Tahun <span class="text-danger">*</span></label>
                            <input type="number" name="period_year" class="form-control" value="{{ now()->year }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Angka Meter Saat Ini (m³) <span class="text-danger">*</span></label>
                        <input type="text" id="masterCurrentReadingDisplay" class="form-control input-ribuan"
                               inputmode="decimal" autocomplete="off" required placeholder="cth: 5.230,50" data-target="#masterCurrentReading">
                        <input type="hidden" name="current_reading" id="masterCurrentReading">
                        <div class="form-text">Angka meter sebelumnya otomatis diambil dari pencatatan periode terakhir.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Tanggal Catat <span class="text-danger">*</span></label>
                        <input type="date" name="reading_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Foto Meteran</label>
                        <input type="file" name="photo" accept="image/*" class="form-control">
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-medium">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Opsional"></textarea>
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
    'entity' => 'Pencatatan Meteran Induk',
])
@endsection

@section('script-bottom')
<script>
(function () {
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/master-meter-readings/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });

    if (typeof feather !== 'undefined') feather.replace();
}());
</script>
@endsection
