@extends('layouts.vertical', ['title' => 'Rekap Keuangan'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Rekap Keuangan</h4>
            <p class="text-muted mb-0 fs-13">Ringkasan pemasukan, pengeluaran, dan saldo kas PDAM</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.financial-report.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <label class="fs-13 fw-semibold mb-0">Tahun:</label>
                <select name="year" class="form-select form-select-sm" style="width:110px" onchange="this.form.submit()">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>

                <label class="fs-13 fw-semibold mb-0 ms-2">Bulan:</label>
                <select name="month" class="form-select form-select-sm" style="width:140px" onchange="this.form.submit()">
                    <option value="">-- Semua Bulan --</option>
                    @foreach ($monthNames as $num => $name)
                        <option value="{{ $num }}" @selected($num == $month)>{{ $name }}</option>
                    @endforeach
                </select>

                @if ($month)
                    <a href="{{ route('admin.financial-report.index', ['year' => $year]) }}" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="x" style="width:13px;height:13px;" class="me-1"></i>Reset Bulan
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 h-100" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0)">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 fw-semibold text-success text-uppercase">
                            {{ $month ? $monthNames[$month] . ' ' . $year : 'Total Masuk ' . $year }}
                        </span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-25" style="width:36px;height:36px">
                            <i data-feather="trending-up" style="width:18px;height:18px;" class="text-success"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-success mb-0">Rp {{ number_format($yearSummary->total_masuk ?? 0, 0, ',', '.') }}</h4>
                    <small class="text-muted">Pemasukan</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 h-100" style="background:linear-gradient(135deg,#fee2e2,#fecaca)">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 fw-semibold text-danger text-uppercase">
                            {{ $month ? $monthNames[$month] . ' ' . $year : 'Total Keluar ' . $year }}
                        </span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-25" style="width:36px;height:36px">
                            <i data-feather="trending-down" style="width:18px;height:18px;" class="text-danger"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-danger mb-0">Rp {{ number_format($yearSummary->total_keluar ?? 0, 0, ',', '.') }}</h4>
                    <small class="text-muted">Pengeluaran</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @php $saldo = $yearSummary->saldo ?? 0; @endphp
            <div class="card border-0 h-100" style="background:linear-gradient(135deg,{{ $saldo >= 0 ? '#dbeafe,#bfdbfe' : '#fee2e2,#fecaca' }})">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-12 fw-semibold text-uppercase" style="color:{{ $saldo >= 0 ? '#1d4ed8' : '#dc2626' }}">
                            Saldo {{ $month ? $monthNames[$month] . ' ' . $year : $year }}
                        </span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:{{ $saldo >= 0 ? 'rgba(59,130,246,.2)' : 'rgba(220,38,38,.2)' }}">
                            <i data-feather="dollar-sign" style="width:18px;height:18px;" class="{{ $saldo >= 0 ? 'text-primary' : 'text-danger' }}"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0" style="color:{{ $saldo >= 0 ? '#1d4ed8' : '#dc2626' }}">
                        {{ $saldo < 0 ? '-' : '' }}Rp {{ number_format(abs($saldo), 0, ',', '.') }}
                    </h4>
                    <small class="text-muted">{{ $saldo >= 0 ? 'Surplus' : 'Defisit' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Tabel Ringkasan Bulanan --}}
        @if (!$month)
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fs-15">Ringkasan Per Bulan — {{ $year }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 fs-13">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Bulan</th>
                                    <th class="text-end">Masuk</th>
                                    <th class="text-end">Keluar</th>
                                    <th class="text-end pe-3">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cumulativeSaldo = 0;
                                    $hasAny = false;
                                @endphp
                                @foreach ($monthNames as $num => $name)
                                    @php
                                        $row = $monthly->get($num);
                                        $masuk  = $row?->total_masuk  ?? 0;
                                        $keluar = $row?->total_keluar ?? 0;
                                        $saldoBulan = $masuk - $keluar;
                                        $cumulativeSaldo += $saldoBulan;
                                        if ($row) $hasAny = true;
                                    @endphp
                                    <tr class="{{ $row ? '' : 'text-muted opacity-50' }}">
                                        <td class="ps-3">
                                            <a href="{{ route('admin.financial-report.index', ['year' => $year, 'month' => $num]) }}"
                                               class="text-decoration-none {{ $row ? 'text-dark fw-semibold' : 'text-muted' }}">
                                                {{ $name }}
                                            </a>
                                        </td>
                                        <td class="text-end {{ $masuk > 0 ? 'text-success fw-semibold' : '' }}">
                                            {{ $masuk > 0 ? 'Rp '.number_format($masuk, 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="text-end {{ $keluar > 0 ? 'text-danger' : '' }}">
                                            {{ $keluar > 0 ? 'Rp '.number_format($keluar, 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="text-end pe-3 fw-semibold {{ $saldoBulan >= 0 ? 'text-primary' : 'text-danger' }}">
                                            {{ $row ? (($saldoBulan < 0 ? '-' : '').'Rp '.number_format(abs($saldoBulan), 0, ',', '.')) : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td class="ps-3">Total</td>
                                    <td class="text-end text-success">Rp {{ number_format($yearSummary->total_masuk ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end text-danger">Rp {{ number_format($yearSummary->total_keluar ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end pe-3 {{ ($yearSummary->saldo ?? 0) >= 0 ? 'text-primary' : 'text-danger' }}">
                                        Rp {{ number_format(abs($yearSummary->saldo ?? 0), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Breakdown Kategori --}}
        <div class="col-lg-{{ $month ? '12' : '5' }}">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0 fs-15">
                        Breakdown Kategori
                        @if ($month) — {{ $monthNames[$month] }} {{ $year }} @else — {{ $year }} @endif
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if ($byCategory->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i data-feather="inbox" style="width:40px;height:40px;" class="mb-2 opacity-50 d-block mx-auto"></i>
                            Belum ada transaksi
                        </div>
                    @else
                        @php
                            $masukKategori  = $byCategory->where('type', 'masuk');
                            $keluarKategori = $byCategory->where('type', 'keluar');
                            $totalMasukKat  = $masukKategori->sum('total');
                            $totalKeluarKat = $keluarKategori->sum('total');
                        @endphp

                        @if ($masukKategori->isNotEmpty())
                        <div class="px-3 pt-3 pb-1">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-success-subtle text-success fs-11">Pemasukan</span>
                            </div>
                            @foreach ($masukKategori as $cat)
                                @php $pct = $totalMasukKat > 0 ? round($cat->total / $totalMasukKat * 100) : 0; @endphp
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between fs-13 mb-1">
                                        <span>{{ $cat->category }} <span class="text-muted fs-11">({{ $cat->jumlah }}x)</span></span>
                                        <span class="fw-semibold text-success">Rp {{ number_format($cat->total, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="progress" style="height:5px">
                                        <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif

                        @if ($keluarKategori->isNotEmpty())
                        <div class="px-3 pt-2 pb-3">
                            <div class="d-flex align-items-center gap-2 mb-2 mt-1">
                                <span class="badge bg-danger-subtle text-danger fs-11">Pengeluaran</span>
                            </div>
                            @foreach ($keluarKategori as $cat)
                                @php $pct = $totalKeluarKat > 0 ? round($cat->total / $totalKeluarKat * 100) : 0; @endphp
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between fs-13 mb-1">
                                        <span>{{ $cat->category }} <span class="text-muted fs-11">({{ $cat->jumlah }}x)</span></span>
                                        <span class="fw-semibold text-danger">Rp {{ number_format($cat->total, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="progress" style="height:5px">
                                        <div class="progress-bar bg-danger" style="width:{{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Detail Transaksi (jika filter bulan dipilih) --}}
        @if ($month && $transactions->isNotEmpty())
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fs-15">Detail Transaksi — {{ $monthNames[$month] }} {{ $year }}</h5>
                    <span class="badge bg-secondary-subtle text-secondary">{{ $transactions->count() }} transaksi</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 fs-13">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Kategori</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Keterangan</th>
                                    <th class="pe-3">Dicatat oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $t)
                                <tr>
                                    <td class="ps-3">{{ $t->transaction_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($t->type === 'masuk')
                                            <span class="badge bg-success-subtle text-success fs-11">Masuk</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger fs-11">Keluar</span>
                                        @endif
                                    </td>
                                    <td>{{ $t->category }}</td>
                                    <td class="text-end fw-semibold {{ $t->type === 'masuk' ? 'text-success' : 'text-danger' }}">
                                        {{ $t->type === 'masuk' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-muted">{{ $t->description ?? '—' }}</td>
                                    <td class="pe-3 text-muted">{{ $t->recordedBy->name ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="ps-3">Total Bulan Ini</td>
                                    <td class="text-end">
                                        <span class="text-success d-block">+Rp {{ number_format($yearSummary->total_masuk, 0, ',', '.') }}</span>
                                        <span class="text-danger d-block">-Rp {{ number_format($yearSummary->total_keluar, 0, ',', '.') }}</span>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection

@section('script-bottom')
<script>
(function () {
    if (typeof feather !== 'undefined') feather.replace();
}());
</script>
@endsection
