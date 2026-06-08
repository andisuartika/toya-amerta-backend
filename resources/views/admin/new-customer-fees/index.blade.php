@extends('layouts.vertical', ['title' => 'Biaya Pemasangan Pelanggan Baru'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Biaya Pemasangan Pelanggan Baru</h4>
            <p class="text-muted mb-0 fs-13">Riwayat pembayaran biaya pendaftaran & instalasi</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i data-feather="users" style="width:14px;height:14px;" class="me-1"></i>Data Pelanggan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 fs-13">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary --}}
    <div class="card mb-3 border-0 bg-success-subtle">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col">
                    <div class="fs-12 text-muted">Total Pemasukan Biaya Pemasangan</div>
                    <div class="fs-22 fw-bold text-success">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </div>
                    <div class="fs-12 text-muted">{{ $fees->count() }} transaksi</div>
                </div>
                <div class="col-auto">
                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="fs-40 text-success opacity-50"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label mb-1 fs-13">Pelanggan</label>
                    <select name="customer_id" id="filterCustomer" class="form-select form-select-sm">
                        <option value="">Semua Pelanggan</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Jenis Biaya</label>
                    <select name="fee_type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="biaya_pendaftaran" {{ request('fee_type') === 'biaya_pendaftaran' ? 'selected' : '' }}>Pendaftaran</option>
                        <option value="biaya_instalasi"   {{ request('fee_type') === 'biaya_instalasi'   ? 'selected' : '' }}>Instalasi</option>
                        <option value="biaya_meteran"     {{ request('fee_type') === 'biaya_meteran'     ? 'selected' : '' }}>Meteran</option>
                        <option value="uang_jaminan"      {{ request('fee_type') === 'uang_jaminan'      ? 'selected' : '' }}>Uang Jaminan</option>
                        <option value="lainnya"           {{ request('fee_type') === 'lainnya'           ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Dari</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1 fs-13">Sampai</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i data-feather="filter" style="width:13px;height:13px;" class="me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.new-customer-fees.index') }}" class="btn btn-outline-secondary btn-sm px-3 ms-1">
                        <i data-feather="x" style="width:13px;height:13px;" class="me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fs-15">Riwayat Biaya Pemasangan</h5>
            <input type="text" id="feeSearch" class="form-control form-control-sm"
                   placeholder="Cari..." style="width:200px">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="feeTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Pelanggan</th>
                            <th>Jenis Biaya</th>
                            <th class="text-end">Jumlah</th>
                            <th>Metode</th>
                            <th class="text-center">Tanggal</th>
                            <th>No. Kwitansi</th>
                            <th class="text-center pe-3" style="width:70px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fees as $fee)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-medium">{{ $fee->customer->name }}</div>
                                <div class="fs-12 text-muted">
                                    {{ $fee->customer->customer_number }}
                                    @if ($fee->customer->zone) · {{ $fee->customer->zone->name }} @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ \App\Models\NewCustomerFee::feeTypeLabel($fee->fee_type) }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($fee->amount, 0, ',', '.') }}
                            </td>
                            <td class="fs-13 text-capitalize">{{ $fee->payment_method }}</td>
                            <td class="text-center fs-13">{{ $fee->payment_date->format('d/m/Y') }}</td>
                            <td class="fs-12 font-monospace text-muted">{{ $fee->receipt_number ?? '—' }}</td>
                            <td class="text-center pe-3">
                                <button class="btn btn-sm bg-danger-subtle btn-delete"
                                    data-id="{{ $fee->id }}"
                                    data-name="{{ $fee->customer->name }} - {{ \App\Models\NewCustomerFee::feeTypeLabel($fee->fee_type) }}"
                                    title="Hapus">
                                    <i data-feather="trash-2" style="width:13px;height:13px;" class="text-danger"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i data-feather="inbox" style="width:32px;height:32px;" class="mb-2 opacity-30 d-block mx-auto"></i>
                                Belum ada data biaya pemasangan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if ($fees->count())
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="ps-3 fw-semibold text-end">Total:</td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>

@include('admin.partials.modal-delete', [
    'entity'  => 'Biaya Pemasangan',
    'warning' => 'Data yang dihapus tidak dapat dikembalikan.',
])
@endsection

@section('script-bottom')
<script>
(function () {
    document.getElementById('feeSearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#feeTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/new-customer-fees/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });
}());
</script>
@endsection
