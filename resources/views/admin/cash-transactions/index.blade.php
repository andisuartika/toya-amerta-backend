@extends('layouts.vertical', ['title' => 'Kas Transaksi'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Kas Transaksi</h4>
            <p class="text-muted mb-0 fs-13">Pencatatan pemasukan dan pengeluaran kas PDAM</p>
        </div>
        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i data-feather="plus" style="width:15px;height:15px;" class="me-1"></i>Tambah Transaksi
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
        <div class="col-sm-4">
            <div class="card border-0 bg-success-subtle">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <iconify-icon icon="solar:arrow-down-bold-duotone" class="fs-30 text-success"></iconify-icon>
                        <div>
                            <div class="fs-12 text-muted">Total Pemasukan</div>
                            <div class="fs-18 fw-bold text-success">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 bg-danger-subtle">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <iconify-icon icon="solar:arrow-up-bold-duotone" class="fs-30 text-danger"></iconify-icon>
                        <div>
                            <div class="fs-12 text-muted">Total Pengeluaran</div>
                            <div class="fs-18 fw-bold text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 {{ $saldo >= 0 ? 'bg-primary-subtle' : 'bg-warning-subtle' }}">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <iconify-icon icon="solar:wallet-bold-duotone" class="fs-30 {{ $saldo >= 0 ? 'text-primary' : 'text-warning' }}"></iconify-icon>
                        <div>
                            <div class="fs-12 text-muted">Saldo Kas</div>
                            <div class="fs-18 fw-bold {{ $saldo >= 0 ? 'text-primary' : 'text-warning' }}">
                                Rp {{ number_format(abs($saldo), 0, ',', '.') }}
                                @if ($saldo < 0) <small class="fs-12">(defisit)</small> @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Jenis</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="masuk"  {{ request('type') === 'masuk'  ? 'selected' : '' }}>Pemasukan</option>
                        <option value="keluar" {{ request('type') === 'keluar' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label mb-1 fs-13">Kategori</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        @foreach (array_merge($categories['masuk'], $categories['keluar']) as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Dari</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Sampai</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i data-feather="filter" style="width:13px;height:13px;" class="me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.cash-transactions.index') }}" class="btn btn-outline-secondary btn-sm px-3 ms-1">
                        <i data-feather="x" style="width:13px;height:13px;" class="me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fs-15">Daftar Transaksi</h5>
            <input type="text" id="kasSearch" class="form-control form-control-sm"
                   placeholder="Cari..." style="width:200px">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="kasTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-center" style="width:110px">Tanggal</th>
                            <th class="text-center" style="width:100px">Jenis</th>
                            <th>Kategori</th>
                            <th class="text-end">Jumlah</th>
                            <th>Keterangan</th>
                            <th class="fs-12 text-muted">Dicatat oleh</th>
                            <th class="text-end pe-3" style="width:90px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $t)
                        <tr>
                            <td class="ps-3 text-center fs-13">{{ $t->transaction_date->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if ($t->type === 'masuk')
                                    <span class="badge bg-success-subtle text-success">
                                        <i data-feather="arrow-down-left" style="width:11px;height:11px;"></i> Masuk
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i data-feather="arrow-up-right" style="width:11px;height:11px;"></i> Keluar
                                    </span>
                                @endif
                            </td>
                            <td class="fs-13">{{ $t->category }}</td>
                            <td class="text-end fw-semibold {{ $t->type === 'masuk' ? 'text-success' : 'text-danger' }}">
                                {{ $t->type === 'masuk' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                            </td>
                            <td class="fs-13 text-muted">{{ $t->description ?? '—' }}</td>
                            <td class="fs-12 text-muted">{{ $t->recordedBy->name ?? '—' }}</td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-1">
                                <button class="btn btn-sm bg-primary-subtle btn-edit"
                                    data-id="{{ $t->id }}"
                                    data-date="{{ $t->transaction_date->format('Y-m-d') }}"
                                    data-type="{{ $t->type }}"
                                    data-category="{{ $t->category }}"
                                    data-amount="{{ (int) $t->amount }}"
                                    data-description="{{ $t->description }}"
                                    title="Edit">
                                    <i data-feather="edit-2" style="width:13px;height:13px;" class="text-primary"></i>
                                </button>
                                <button class="btn btn-sm bg-danger-subtle btn-delete"
                                    data-id="{{ $t->id }}"
                                    data-name="{{ $t->category }} - {{ $t->transaction_date->format('d/m/Y') }}"
                                    title="Hapus">
                                    <i data-feather="trash-2" style="width:13px;height:13px;" class="text-danger"></i>
                                </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i data-feather="inbox" style="width:32px;height:32px;" class="mb-2 opacity-30 d-block mx-auto"></i>
                                Belum ada transaksi kas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if ($transactions->count())
                    <tfoot class="table-light fw-semibold">
                        <tr>
                            <td colspan="3" class="ps-3 text-end">Saldo Periode:</td>
                            <td class="text-end {{ $saldo >= 0 ? 'text-primary' : 'text-danger' }}">
                                Rp {{ number_format($saldo, 0, ',', '.') }}
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

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.cash-transactions.store') }}" id="formTambah">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Tambah Transaksi Kas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.cash-transactions._form')
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formEdit">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Edit Transaksi Kas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.cash-transactions._form', ['edit' => true])
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
    'entity'  => 'Transaksi Kas',
    'warning' => 'Transaksi yang dihapus tidak dapat dikembalikan.',
])
@endsection

@section('script-bottom')
<script>
(function () {
    var categoriesMasuk  = @json($categories['masuk']);
    var categoriesKeluar = @json($categories['keluar']);

    function formatRibuan(val) {
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Update kategori dropdown berdasarkan jenis
    function updateCategories(typeEl, categoryEl) {
        var type = typeEl.value;
        var cats = type === 'masuk' ? categoriesMasuk : categoriesKeluar;
        var current = categoryEl.value;
        categoryEl.innerHTML = '<option value="">-- Pilih Kategori --</option>';
        cats.forEach(function (c) {
            var opt = document.createElement('option');
            opt.value = c; opt.textContent = c;
            if (c === current) opt.selected = true;
            categoryEl.appendChild(opt);
        });
        // kategori custom
        var customOpt = document.createElement('option');
        customOpt.value = '__custom__'; customOpt.textContent = 'Lainnya (ketik manual)';
        categoryEl.appendChild(customOpt);
    }

    // Init dropdown kategori untuk kedua form
    ['formTambah', 'formEdit'].forEach(function (formId) {
        var form = document.getElementById(formId);
        if (!form) return;
        var typeEl     = form.querySelector('[name="type"]');
        var categoryEl = form.querySelector('[name="category"]');
        var customEl   = form.querySelector('.category-custom');

        typeEl.addEventListener('change', function () {
            updateCategories(typeEl, categoryEl);
            customEl.style.display = 'none';
            customEl.value = '';
        });
        categoryEl.addEventListener('change', function () {
            customEl.style.display = this.value === '__custom__' ? '' : 'none';
            if (this.value !== '__custom__') customEl.value = '';
        });

        // Format ribuan
        var displayEl = form.querySelector('.amount-display');
        var hiddenEl  = form.querySelector('[name="amount"]');
        displayEl.addEventListener('input', function () {
            var raw = this.value.replace(/[^\d]/g, '');
            var num = parseInt(raw, 10) || 0;
            var cursor = this.selectionStart, oldLen = this.value.length;
            this.value = raw ? formatRibuan(num) : '';
            hiddenEl.value = num > 0 ? num : '';
            var newLen = this.value.length;
            this.setSelectionRange(cursor + (newLen - oldLen), cursor + (newLen - oldLen));
        });
    });

    // Edit modal
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-edit');
        if (!btn) return;
        var d    = btn.dataset;
        var form = document.getElementById('formEdit');
        form.action = '/admin/cash-transactions/' + d.id;

        form.querySelector('[name="transaction_date"]').value = d.date;
        form.querySelector('[name="type"]').value             = d.type;

        var categoryEl = form.querySelector('[name="category"]');
        var customEl   = form.querySelector('.category-custom');
        updateCategories(form.querySelector('[name="type"]'), categoryEl);

        // Cek apakah kategori ada di list
        var found = Array.from(categoryEl.options).some(o => o.value === d.category);
        if (found) {
            categoryEl.value = d.category;
            customEl.style.display = 'none';
        } else {
            categoryEl.value = '__custom__';
            customEl.style.display = '';
            customEl.value = d.category;
        }

        var num = parseInt(d.amount, 10) || 0;
        form.querySelector('.amount-display').value = num ? formatRibuan(num) : '';
        form.querySelector('[name="amount"]').value = num || '';
        form.querySelector('[name="description"]').value = d.description || '';

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    });

    // Delete modal
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/cash-transactions/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });

    // Search
    document.getElementById('kasSearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#kasTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    // Init tambah modal on open
    document.getElementById('modalTambah').addEventListener('show.bs.modal', function () {
        var form       = document.getElementById('formTambah');
        var typeEl     = form.querySelector('[name="type"]');
        var categoryEl = form.querySelector('[name="category"]');
        typeEl.value   = 'masuk';
        updateCategories(typeEl, categoryEl);
        form.querySelector('.amount-display').value = '';
        form.querySelector('[name="amount"]').value = '';
        form.querySelector('[name="description"]').value = '';
        form.querySelector('.category-custom').style.display = 'none';
    });

    if (typeof feather !== 'undefined') feather.replace();
}());
</script>
@endsection
