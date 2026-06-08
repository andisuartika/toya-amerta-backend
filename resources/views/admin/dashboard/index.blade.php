@extends('layouts.vertical', ['title' => 'Dashboard'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
            <p class="text-muted mb-0 fs-13">Selamat datang, {{ auth()->user()->name }}</p>
        </div>
        <div class="text-end">
            <small class="text-muted">{{ now()->isoFormat('dddd, D MMMM Y') }}</small>
        </div>
    </div>

    {{-- ── Row 1: Stats utama ── --}}
    <div class="row g-3">
        {{-- Saldo Kas --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fs-12 fw-semibold text-uppercase opacity-75">Saldo Kas</div>
                        <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width:38px;height:38px">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" style="font-size:20px"></iconify-icon>
                        </div>
                    </div>
                    <h3 class="fw-bold text-white mb-0">Rp {{ number_format(abs($saldoKas), 0, ',', '.') }}</h3>
                    <small class="opacity-75">{{ $saldoKas >= 0 ? 'Surplus' : 'Defisit' }} keseluruhan</small>
                </div>
            </div>
        </div>

        {{-- Masuk bulan ini --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fs-12 fw-semibold text-muted text-uppercase">Pemasukan {{ $monthNames[$month] }}</div>
                        <span class="avatar-sm">
                            <span class="avatar-title bg-soft-success rounded fs-20">
                                <iconify-icon icon="solar:arrow-up-bold-duotone" class="text-success"></iconify-icon>
                            </span>
                        </span>
                    </div>
                    <h4 class="fw-bold text-success mb-0">Rp {{ number_format($kasbulan->masuk ?? 0, 0, ',', '.') }}</h4>
                    <small class="text-muted">Total pemasukan kas bulan ini</small>
                </div>
            </div>
        </div>

        {{-- Keluar bulan ini --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fs-12 fw-semibold text-muted text-uppercase">Pengeluaran {{ $monthNames[$month] }}</div>
                        <span class="avatar-sm">
                            <span class="avatar-title bg-soft-danger rounded fs-20">
                                <iconify-icon icon="solar:arrow-down-bold-duotone" class="text-danger"></iconify-icon>
                            </span>
                        </span>
                    </div>
                    <h4 class="fw-bold text-danger mb-0">Rp {{ number_format($kasbulan->keluar ?? 0, 0, ',', '.') }}</h4>
                    <small class="text-muted">Total pengeluaran kas bulan ini</small>
                </div>
            </div>
        </div>

        {{-- Pelanggan aktif --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fs-12 fw-semibold text-muted text-uppercase">Pelanggan Aktif</div>
                        <span class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded fs-20">
                                <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="text-primary"></iconify-icon>
                            </span>
                        </span>
                    </div>
                    <h4 class="fw-bold mb-0">{{ number_format($stats['total_customers']) }}</h4>
                    <small class="text-muted">{{ $stats['total_zones'] }} zona wilayah</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 2: Tagihan bulan ini + Maintenance + PDAM Pusat ── --}}
    <div class="row g-3 mt-1">
        {{-- Tagihan bulan ini --}}
        <div class="col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fs-14">Tagihan {{ $monthNames[$month] }} {{ $year }}</h5>
                    <a href="{{ route('admin.payments.index') }}" class="fs-12 text-primary">Lihat semua</a>
                </div>
                <div class="card-body">
                    @php
                        $total    = $tagihanBulanIni->total ?? 0;
                        $lunas    = $tagihanBulanIni->lunas ?? 0;
                        $belum    = $tagihanBulanIni->belum_bayar ?? 0;
                        $sebagian = $tagihanBulanIni->sebagian ?? 0;
                        $pct      = $total > 0 ? round($lunas / $total * 100) : 0;
                        $terkumpul = $tagihanBulanIni->terkumpul ?? 0;
                        $totalAmt  = $tagihanBulanIni->total_amount ?? 0;
                    @endphp

                    <div class="d-flex justify-content-between mb-1 fs-13">
                        <span class="text-muted">Progress Pelunasan</span>
                        <span class="fw-semibold">{{ $pct }}%</span>
                    </div>
                    <div class="progress mb-3" style="height:8px">
                        <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                    </div>

                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="p-2 rounded bg-success-subtle">
                                <div class="fw-bold fs-16 text-success">{{ $lunas }}</div>
                                <div class="fs-11 text-muted">Lunas</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-warning-subtle">
                                <div class="fw-bold fs-16 text-warning">{{ $sebagian }}</div>
                                <div class="fs-11 text-muted">Sebagian</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-danger-subtle">
                                <div class="fw-bold fs-16 text-danger">{{ $belum }}</div>
                                <div class="fs-11 text-muted">Belum</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">
                    <div class="d-flex justify-content-between fs-13">
                        <span class="text-muted">Terkumpul</span>
                        <span class="fw-semibold text-success">Rp {{ number_format($terkumpul, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between fs-13">
                        <span class="text-muted">Total Tagihan</span>
                        <span class="fw-semibold">Rp {{ number_format($totalAmt, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Maintenance Aktif --}}
        <div class="col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fs-14">Maintenance Aktif</h5>
                    <a href="{{ route('admin.maintenances.index') }}" class="fs-12 text-primary">Lihat semua</a>
                </div>
                <div class="card-body">
                    @php
                        $mTotal   = $maintenanceAktif->total   ?? 0;
                        $mDarurat = $maintenanceAktif->darurat ?? 0;
                        $mTinggi  = $maintenanceAktif->tinggi  ?? 0;
                    @endphp
                    @if ($mTotal === 0)
                        <div class="text-center py-3 text-muted">
                            <iconify-icon icon="solar:check-circle-bold-duotone" class="text-success" style="font-size:40px"></iconify-icon>
                            <p class="mt-2 mb-0 fs-13">Tidak ada maintenance aktif</p>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center" style="width:52px;height:52px;flex-shrink:0">
                                <span class="fw-bold fs-20 text-primary">{{ $mTotal }}</span>
                            </div>
                            <div>
                                <div class="fw-semibold">Pekerjaan Aktif</div>
                                <div class="fs-12 text-muted">Dilaporkan & dalam proses</div>
                            </div>
                        </div>

                        @if ($mDarurat > 0)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-danger-subtle mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i data-feather="alert-triangle" style="width:15px;height:15px;" class="text-danger"></i>
                                <span class="fs-13 fw-semibold text-danger">Prioritas Darurat</span>
                            </div>
                            <span class="badge bg-danger">{{ $mDarurat }}</span>
                        </div>
                        @endif

                        @if ($mTinggi > 0)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-warning-subtle mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i data-feather="alert-circle" style="width:15px;height:15px;" class="text-warning"></i>
                                <span class="fs-13 fw-semibold text-warning">Prioritas Tinggi</span>
                            </div>
                            <span class="badge bg-warning text-dark">{{ $mTinggi }}</span>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Tagihan PDAM Pusat --}}
        <div class="col-xl-4 col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fs-14">Tagihan PDAM Pusat</h5>
                    <a href="{{ route('admin.pdam-bills.index') }}" class="fs-12 text-primary">Lihat semua</a>
                </div>
                <div class="card-body p-0">
                    @if ($pdamBelumLunas->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <iconify-icon icon="solar:check-circle-bold-duotone" class="text-success" style="font-size:40px"></iconify-icon>
                            <p class="mt-2 mb-0 fs-13">Semua tagihan PDAM lunas</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach ($pdamBelumLunas->take(3) as $bill)
                            <div class="list-group-item border-0 px-3 py-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold fs-13">{{ $bill->period_label }}</div>
                                        <div class="fs-11 {{ $bill->is_overdue ? 'text-danger' : 'text-muted' }}">
                                            Jatuh tempo: {{ $bill->due_date->format('d/m/Y') }}
                                            @if ($bill->is_overdue)
                                                <span class="badge bg-danger-subtle text-danger ms-1">Terlambat</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="fw-bold fs-13 text-danger">Rp {{ number_format($bill->total_bill_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if ($pdamBelumLunas->count() > 3)
                            <div class="text-center py-2 fs-12 text-muted border-top">
                                +{{ $pdamBelumLunas->count() - 3 }} tagihan lainnya
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 3: Grafik + Transaksi terbaru ── --}}
    <div class="row g-3 mt-1">
        {{-- Grafik tren 6 bulan --}}
        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fs-14">Tren Kas 6 Bulan Terakhir</h5>
                    <a href="{{ route('admin.financial-report.index') }}" class="fs-12 text-primary">Rekap Keuangan</a>
                </div>
                <div class="card-body">
                    <div id="chartTren"></div>
                </div>
            </div>
        </div>

        {{-- Transaksi terbaru --}}
        <div class="col-xl-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fs-14">Transaksi Kas Terbaru</h5>
                    <a href="{{ route('admin.cash-transactions.index') }}" class="fs-12 text-primary">Lihat semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse ($transaksiTerbaru as $t)
                        <div class="list-group-item border-0 px-3 py-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0
                                    {{ $t->type === 'masuk' ? 'bg-success-subtle' : 'bg-danger-subtle' }}"
                                    style="width:34px;height:34px">
                                    <i data-feather="{{ $t->type === 'masuk' ? 'arrow-down-left' : 'arrow-up-right' }}"
                                       style="width:14px;height:14px;"
                                       class="{{ $t->type === 'masuk' ? 'text-success' : 'text-danger' }}"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fs-13 fw-semibold text-truncate">{{ $t->category }}</div>
                                    <div class="fs-11 text-muted">{{ $t->transaction_date->format('d/m/Y') }}</div>
                                </div>
                                <div class="fs-13 fw-bold {{ $t->type === 'masuk' ? 'text-success' : 'text-danger' }}">
                                    {{ $t->type === 'masuk' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted fs-13">Belum ada transaksi</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Row 4: Pembayaran terbaru + Quick Access ── --}}
    <div class="row g-3 mt-1">
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fs-14">Pembayaran Terbaru</h5>
                    <a href="{{ route('admin.payments.history') }}" class="fs-12 text-primary">Riwayat lengkap</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 fs-13">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Pelanggan</th>
                                    <th>Periode</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Status</th>
                                    <th class="pe-3">Tgl Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pembayaranTerbaru as $p)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $p->customer->name ?? '—' }}</td>
                                    <td class="text-muted">{{ $p->waterReading?->period_label ?? '—' }}</td>
                                    <td class="text-end text-success fw-semibold">Rp {{ number_format($p->amount_paid, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($p->status === 'lunas')
                                            <span class="badge bg-success-subtle text-success fs-11">Lunas</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning fs-11">Sebagian</span>
                                        @endif
                                    </td>
                                    <td class="pe-3 text-muted">{{ $p->payment_date->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada pembayaran</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 fs-14">Akses Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @php
                        $menus = [
                            ['route' => 'admin.water-readings.index',   'icon' => 'solar:ruler-pen-bold-duotone',            'color' => 'primary',  'label' => 'Input Meteran'],
                            ['route' => 'admin.payments.index',         'icon' => 'solar:dollar-minimalistic-bold-duotone',  'color' => 'success',  'label' => 'Konfirmasi Bayar'],
                            ['route' => 'admin.maintenances.index',     'icon' => 'solar:settings-bold-duotone',             'color' => 'warning',  'label' => 'Maintenance'],
                            ['route' => 'admin.cash-transactions.index','icon' => 'solar:money-bag-bold-duotone',            'color' => 'info',     'label' => 'Kas Transaksi'],
                            ['route' => 'admin.customers.index',        'icon' => 'solar:users-group-rounded-bold-duotone',  'color' => 'primary',  'label' => 'Pelanggan'],
                            ['route' => 'admin.financial-report.index', 'icon' => 'solar:graph-up-bold-duotone',             'color' => 'success',  'label' => 'Rekap Keuangan'],
                        ];
                        @endphp
                        @foreach ($menus as $m)
                        <div class="col-4">
                            <a href="{{ route($m['route']) }}" class="text-decoration-none">
                                <div class="p-2 border rounded-3 text-center h-100">
                                    <iconify-icon icon="{{ $m['icon'] }}" class="text-{{ $m['color'] }} mb-1 d-block" style="font-size:26px"></iconify-icon>
                                    <p class="mb-0 fw-medium fs-12 text-dark">{{ $m['label'] }}</p>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script-bottom')
<script>
(function () {
    var labels  = @json($trenLabels);
    var masuk   = @json($trenMasuk);
    var keluar  = @json($trenKeluar);

    var options = {
        series: [
            { name: 'Pemasukan',   data: masuk   },
            { name: 'Pengeluaran', data: keluar  },
        ],
        chart: {
            type: 'bar',
            height: 260,
            toolbar: { show: false },
            fontFamily: 'inherit',
        },
        colors: ['#22c55e', '#ef4444'],
        plotOptions: {
            bar: { columnWidth: '50%', borderRadius: 4, grouped: true },
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: labels,
            labels: { style: { fontSize: '11px' } },
        },
        yaxis: {
            labels: {
                formatter: function (v) {
                    if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(1) + ' jt';
                    if (v >= 1000)    return 'Rp ' + (v / 1000).toFixed(0) + ' rb';
                    return 'Rp ' + v;
                },
                style: { fontSize: '11px' },
            },
        },
        legend: { position: 'top', fontSize: '12px' },
        grid: { borderColor: '#f1f1f1' },
        tooltip: {
            y: {
                formatter: function (v) {
                    return 'Rp ' + parseInt(v).toLocaleString('id-ID');
                },
            },
        },
    };

    if (typeof ApexCharts !== 'undefined') {
        new ApexCharts(document.getElementById('chartTren'), options).render();
    }

    if (typeof feather !== 'undefined') feather.replace();
}());
</script>
@endsection
