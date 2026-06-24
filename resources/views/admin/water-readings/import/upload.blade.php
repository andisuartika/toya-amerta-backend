@extends('layouts.vertical', ['title' => 'Import Data Historis'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Import Data Historis Pencatatan Meter</h4>
            <p class="text-muted mb-0 fs-13">Upload rekap Excel bulanan (data manual sebelum sistem digunakan)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.water-readings.import.template') }}" class="btn btn-outline-success btn-sm px-3">
                <i data-feather="download" style="width:14px;height:14px;" class="me-1"></i>Download Template Excel
            </a>
            <a href="{{ route('admin.water-readings.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i data-feather="arrow-left" style="width:14px;height:14px;" class="me-1"></i>Kembali ke Pencatatan
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger py-2 fs-13">{!! session('error') !!}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success py-2 fs-13">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 fs-15">
                        <i data-feather="upload" style="width:16px;height:16px;" class="me-1 text-primary"></i>
                        Upload File Excel
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.water-readings.import.preview') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-medium">Bulan <span class="text-danger">*</span></label>
                                <select name="month" class="form-select" required>
                                    @foreach(range(1,12) as $m)
                                        <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('month')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium">Tahun <span class="text-danger">*</span></label>
                                <select name="year" class="form-select" required>
                                    @foreach(range(now()->year, 2025) as $y)
                                        <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                                @error('year')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">File Excel <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Format kolom: <strong>PELANGGAN, METER AWAL, METER AKHIR, HARGA/M3, TOTAL</strong>. Baris ringkasan (TOTAL/GRAND TOTAL/BIAYA ADMIN/PEMBAYARAN PDAM) otomatis dilewati.</div>
                            @error('file')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Biaya Admin Bulan Ini (opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="biaya_admin" class="form-control" min="0" step="1" placeholder="0" value="{{ old('biaya_admin') }}">
                            </div>
                            <div class="form-text">Jika diisi, akan otomatis tercatat sebagai 1 transaksi kas keluar kategori "Biaya Administrasi" untuk periode ini.</div>
                            @error('biaya_admin')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Pembayaran ke PDAM Pusat Bulan Ini (opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="pdam_payment" class="form-control" min="0" step="1" placeholder="0" value="{{ old('pdam_payment') }}">
                            </div>
                            <div class="form-text">Jika diisi, akan otomatis dibuat 1 tagihan PDAM Pusat (status lunas) + tercatat sebagai kas keluar kategori "Tagihan PDAM Pusat" untuk periode ini. Dilewati jika tagihan PDAM periode ini sudah ada.</div>
                            @error('pdam_payment')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i data-feather="eye" style="width:15px;height:15px;" class="me-1"></i>Lihat Preview &amp; Pencocokan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 fs-15">
                        <i data-feather="info" style="width:16px;height:16px;" class="me-1 text-info"></i>
                        Cara Kerja Import
                    </h5>
                </div>
                <div class="card-body">
                    <ol class="fs-13 ps-3 mb-0">
                        <li class="mb-2">Pilih periode (bulan &amp; tahun) yang sesuai dengan isi file Excel — 1 file mewakili 1 periode.</li>
                        <li class="mb-2">Upload file, lalu sistem akan mencoba mencocokkan setiap baris "PELANGGAN" dengan data pelanggan terdaftar berdasarkan nama.</li>
                        <li class="mb-2">Pada halaman preview, periksa hasil pencocokan. Baris yang tidak cocok otomatis bisa dipilih manual.</li>
                        <li class="mb-2">Atur status pembayaran tiap baris (Belum Bayar / Sebagian / Lunas) sebelum disimpan.</li>
                        <li class="mb-2">Baris yang pelanggannya sudah punya pencatatan di periode yang sama akan otomatis dilewati untuk mencegah duplikasi.</li>
                        <li>Ulangi proses ini satu per satu untuk setiap bulan dari Januari 2025 sampai Juni 2026.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
