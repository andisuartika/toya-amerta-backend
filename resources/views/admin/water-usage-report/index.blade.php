@extends('layouts.vertical', ['title' => 'Laporan Penggunaan Air'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Laporan Penggunaan Air</h4>
            <p class="text-muted mb-0 fs-13">Data pemakaian air per pelanggan — filter berdasarkan pelanggan, bulan, dan tahun</p>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.water-usage-report.index') }}" id="filterForm">
                <div class="row g-2 align-items-end">
                    {{-- Pelanggan --}}
                    <div class="col-md-4">
                        <label class="form-label fs-12 fw-semibold mb-1">Pelanggan</label>
                        <select name="customer_id" class="form-select form-select-sm" id="selectCustomer">
                            <option value="">— Semua Pelanggan —</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" @selected($c->id == $customer_id)>
                                    [{{ $c->customer_number }}] {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tahun --}}
                    <div class="col-md-2">
                        <label class="form-label fs-12 fw-semibold mb-1">Tahun</label>
                        <select name="year" class="form-select form-select-sm">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Bulan --}}
                    <div class="col-md-2">
                        <label class="form-label fs-12 fw-semibold mb-1">Bulan</label>
                        <select name="month" class="form-select form-select-sm">
                            <option value="">— Semua Bulan —</option>
                            @foreach($monthNames as $num => $name)
                                <option value="{{ $num }}" @selected($num == $month)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="col-md-4 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i data-feather="search" style="width:13px;height:13px" class="me-1"></i>Tampilkan
                        </button>
                        <a href="{{ route('admin.water-usage-report.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i data-feather="x" style="width:13px;height:13px" class="me-1"></i>Reset
                        </a>

                        @if($readings->count() > 0)
                            <div class="dropdown ms-auto">
                                <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i data-feather="download" style="width:13px;height:13px" class="me-1"></i>Download
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item fs-13"
                                           href="{{ route('admin.water-usage-report.excel', request()->query()) }}">
                                            <i data-feather="file-text" style="width:13px;height:13px" class="me-2 text-success"></i>
                                            Download Excel (.xlsx)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item fs-13"
                                           href="{{ route('admin.water-usage-report.pdf', request()->query()) }}">
                                            <i data-feather="file" style="width:13px;height:13px" class="me-2 text-danger"></i>
                                            Download PDF
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    @if($readings->count() > 0)
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 h-100" style="background:linear-gradient(135deg,#E3F2FD,#BBDEFB)">
                <div class="card-body py-3">
                    <p class="fs-12 fw-semibold text-uppercase mb-1" style="color:#1565C0">Total Pencatatan</p>
                    <h4 class="fw-bold mb-0" style="color:#1565C0">{{ number_format($summary['total_readings']) }}</h4>
                    <small class="text-muted">data pembacaan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 h-100" style="background:linear-gradient(135deg,#E0F7FA,#B2EBF2)">
                <div class="card-body py-3">
                    <p class="fs-12 fw-semibold text-uppercase mb-1" style="color:#00ACC1">Total Pemakaian</p>
                    <h4 class="fw-bold mb-0" style="color:#00ACC1">{{ number_format($summary['usage_m3'], 2) }} <small class="fs-14">m³</small></h4>
                    <small class="text-muted">konsumsi air</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 h-100" style="background:linear-gradient(135deg,#FFF8E1,#FFECB3)">
                <div class="card-body py-3">
                    <p class="fs-12 fw-semibold text-uppercase mb-1" style="color:#F57C00">Total Tagihan</p>
                    <h5 class="fw-bold mb-0" style="color:#F57C00">Rp {{ number_format($summary['total_bill'], 0, ',', '.') }}</h5>
                    <small class="text-muted">keseluruhan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 h-100" style="background:linear-gradient(135deg,#E8F5E9,#C8E6C9)">
                <div class="card-body py-3">
                    <p class="fs-12 fw-semibold text-uppercase mb-1 text-success">Sudah Lunas</p>
                    <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($summary['paid'], 0, ',', '.') }}</h5>
                    <small class="text-muted">terbayar</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 h-100" style="background:linear-gradient(135deg,#FFEBEE,#FFCDD2)">
                <div class="card-body py-3">
                    <p class="fs-12 fw-semibold text-uppercase mb-1 text-danger">Belum Lunas</p>
                    <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($summary['unpaid'], 0, ',', '.') }}</h5>
                    <small class="text-muted">outstanding</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter Info Badge --}}
    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
        <span class="fs-12 text-muted">Filter aktif:</span>
        <span class="badge bg-primary bg-opacity-10 text-primary fs-11">
            Tahun {{ $year }}
        </span>
        @if($month)
            <span class="badge bg-info bg-opacity-10 text-info fs-11">
                {{ $monthNames[$month] }}
            </span>
        @else
            <span class="badge bg-secondary bg-opacity-10 text-secondary fs-11">Semua Bulan</span>
        @endif
        @if($customer_id)
            @php $selectedCustomer = $customers->firstWhere('id', $customer_id); @endphp
            <span class="badge bg-warning bg-opacity-15 text-warning-emphasis fs-11">
                {{ $selectedCustomer?->name ?? 'Pelanggan #'.$customer_id }}
            </span>
        @else
            <span class="badge bg-secondary bg-opacity-10 text-secondary fs-11">Semua Pelanggan</span>
        @endif
        <span class="badge bg-dark bg-opacity-10 text-dark fs-11">
            {{ $readings->count() }} data
        </span>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0 fs-13" id="reportTable">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3" style="width:40px">No</th>
                            <th>No. Pelanggan</th>
                            <th>Nama Pelanggan</th>
                            <th>Zona</th>
                            <th>Periode</th>
                            <th class="text-end">Meter Awal (m³)</th>
                            <th class="text-end">Meter Akhir (m³)</th>
                            <th class="text-end">Pemakaian (m³)</th>
                            <th class="text-end">Tagihan (Rp)</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($readings as $i => $r)
                            @php $usage = $r->current_reading - $r->previous_reading; @endphp
                            <tr>
                                <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fs-11 fw-semibold">
                                        {{ $r->customer->customer_number ?? '-' }}
                                    </span>
                                </td>
                                <td class="fw-semibold">{{ $r->customer->name ?? '-' }}</td>
                                <td class="text-muted">{{ $r->customer->zone->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fs-11">
                                        {{ $monthNames[$r->period_month] ?? $r->period_month }} {{ $r->period_year }}
                                    </span>
                                </td>
                                <td class="text-end text-muted">{{ number_format($r->previous_reading, 2) }}</td>
                                <td class="text-end text-muted">{{ number_format($r->current_reading, 2) }}</td>
                                <td class="text-end fw-semibold" style="color:#1565C0">
                                    {{ number_format($usage, 2) }}
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($r->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if($r->payment_status === 'lunas')
                                        <span class="badge bg-success-subtle text-success fs-11">Lunas</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger fs-11">Belum Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" style="width:32px;height:32px;opacity:.3" class="d-block mx-auto mb-2"></i>
                                    Tidak ada data untuk filter yang dipilih
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($readings->count() > 0)
                    <tfoot>
                        <tr style="background:#E3F2FD;">
                            <td colspan="7" class="text-end fw-bold ps-3" style="color:#1565C0">TOTAL</td>
                            <td class="text-end fw-bold" style="color:#1565C0">
                                {{ number_format($summary['usage_m3'], 2) }} m³
                            </td>
                            <td class="text-end fw-bold" style="color:#1565C0">
                                Rp {{ number_format($summary['total_bill'], 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
