<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1A1A2E; }

    .header { background: #1565C0; color: white; padding: 12px 16px; margin-bottom: 12px; }
    .header-title { font-size: 14px; font-weight: bold; margin-bottom: 3px; }
    .header-sub { font-size: 9px; color: rgba(255,255,255,0.75); }
    .header-meta { font-size: 8px; color: rgba(255,255,255,0.55); margin-top: 4px; }

    .filter-info {
        background: #E3F2FD; border-left: 3px solid #1565C0;
        padding: 6px 10px; margin-bottom: 12px; font-size: 8.5px; color: #546E7A;
    }

    .summary-grid { display: flex; gap: 8px; margin-bottom: 14px; }
    .summary-card {
        flex: 1; border: 1px solid #E0E0E0; border-radius: 4px;
        padding: 7px 10px; background: #FAFAFA;
    }
    .sc-label { font-size: 7.5px; color: #78909C; text-transform: uppercase; letter-spacing: .04em; }
    .sc-value { font-size: 12px; font-weight: bold; color: #1565C0; margin-top: 2px; }
    .sc-value.green { color: #388E3C; }
    .sc-value.red   { color: #D32F2F; }

    table { width: 100%; border-collapse: collapse; }
    thead tr th {
        background: #1565C0; color: white; padding: 6px 7px;
        font-size: 8px; font-weight: bold; text-align: left;
        border: 1px solid #0D47A1;
    }
    thead tr th.num { text-align: center; }
    tbody tr td {
        padding: 5px 7px; border: 1px solid #E0E0E0;
        font-size: 8px; vertical-align: middle;
    }
    tbody tr:nth-child(even) td { background: #F5F9FF; }
    tbody tr:last-child td { border-bottom: 1px solid #BDBDBD; }

    .td-center { text-align: center; }
    .td-right  { text-align: right; }
    .badge-lunas  { background: #C8E6C9; color: #1B5E20; padding: 2px 6px; border-radius: 10px; font-size: 7.5px; font-weight: bold; }
    .badge-belum  { background: #FFCDD2; color: #B71C1C; padding: 2px 6px; border-radius: 10px; font-size: 7.5px; font-weight: bold; }

    tfoot tr td {
        background: #E3F2FD; font-weight: bold; color: #1565C0;
        padding: 6px 7px; border: 1.5px solid #1565C0; font-size: 8.5px;
    }

    .footer { margin-top: 16px; border-top: 1px solid #E0E0E0; padding-top: 8px; display: flex; justify-content: space-between; }
    .footer-left  { font-size: 7.5px; color: #78909C; }
    .footer-right { font-size: 7.5px; color: #78909C; text-align: right; }

    .ttd-section { margin-top: 24px; display: flex; justify-content: flex-end; }
    .ttd-box { text-align: center; font-size: 8px; color: #546E7A; }
    .ttd-line { border-top: 1px solid #9E9E9E; width: 140px; margin: 40px auto 4px; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="header-title">
        Laporan Penggunaan Air Bersih — Toya Amerta
    </div>
    <div class="header-sub">
        Pengelolaan Air PDAM Swadaya · Lingkungan Sangket, Desa Buleleng
    </div>
    <div class="header-meta">
        Periode:
        @if($month)
            {{ $monthNames[$month] ?? $month }} {{ $year }}
        @else
            Tahun {{ $year }}
        @endif
        @if($customer_id && isset($readings[0]))
            &nbsp;·&nbsp; Pelanggan: {{ $readings[0]->customer->name ?? '-' }}
        @endif
        &nbsp;·&nbsp; Dicetak: {{ now()->format('d M Y, H:i') }} WITA
    </div>
</div>

{{-- Summary --}}
<div class="summary-grid">
    <div class="summary-card">
        <div class="sc-label">Total Pencatatan</div>
        <div class="sc-value">{{ number_format($summary['total_readings']) }}</div>
    </div>
    <div class="summary-card">
        <div class="sc-label">Total Pemakaian</div>
        <div class="sc-value">{{ number_format($summary['usage_m3'], 2) }} m³</div>
    </div>
    <div class="summary-card">
        <div class="sc-label">Total Tagihan</div>
        <div class="sc-value">Rp {{ number_format($summary['total_bill'], 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="sc-label">Sudah Lunas</div>
        <div class="sc-value green">Rp {{ number_format($summary['paid'], 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="sc-label">Belum Lunas</div>
        <div class="sc-value red">Rp {{ number_format($summary['unpaid'], 0, ',', '.') }}</div>
    </div>
</div>

{{-- Table --}}
<table>
    <thead>
        <tr>
            <th class="num" style="width:24px">No</th>
            <th style="width:70px">No. Pelanggan</th>
            <th>Nama Pelanggan</th>
            <th style="width:60px">Zona</th>
            <th style="width:70px">Periode</th>
            <th class="num" style="width:60px">Meter Awal</th>
            <th class="num" style="width:60px">Meter Akhir</th>
            <th class="num" style="width:55px">Pemakaian</th>
            <th class="num" style="width:75px">Tagihan (Rp)</th>
            <th class="num" style="width:60px">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($readings as $i => $r)
            @php $usage = $r->current_reading - $r->previous_reading; @endphp
            <tr>
                <td class="td-center">{{ $i + 1 }}</td>
                <td>{{ $r->customer->customer_number ?? '-' }}</td>
                <td>{{ $r->customer->name ?? '-' }}</td>
                <td>{{ $r->customer->zone->name ?? '-' }}</td>
                <td>{{ $monthNames[$r->period_month] ?? $r->period_month }} {{ $r->period_year }}</td>
                <td class="td-right">{{ number_format($r->previous_reading, 2) }}</td>
                <td class="td-right">{{ number_format($r->current_reading, 2) }}</td>
                <td class="td-right">{{ number_format($usage, 2) }}</td>
                <td class="td-right">{{ number_format($r->total_amount, 0, ',', '.') }}</td>
                <td class="td-center">
                    @if($r->payment_status === 'lunas')
                        <span class="badge-lunas">Lunas</span>
                    @else
                        <span class="badge-belum">Belum</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="td-center" style="padding:14px;color:#78909C;">
                    Tidak ada data untuk filter yang dipilih.
                </td>
            </tr>
        @endforelse
    </tbody>
    @if($readings->count() > 0)
    <tfoot>
        <tr>
            <td colspan="7" class="td-right">TOTAL</td>
            <td class="td-right">{{ number_format($summary['usage_m3'], 2) }} m³</td>
            <td class="td-right">Rp {{ number_format($summary['total_bill'], 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- TTD --}}
<div class="ttd-section">
    <div class="ttd-box">
        <div>Mengetahui,</div>
        <div>Pengurus Toya Amerta</div>
        <div class="ttd-line"></div>
        <div>( ______________________ )</div>
    </div>
</div>

{{-- Footer --}}
<div class="footer">
    <div class="footer-left">
        Toya Amerta · Pengelolaan Air Bersih Lingkungan Sangket<br>
        Dokumen ini digenerate otomatis oleh sistem
    </div>
    <div class="footer-right">
        Halaman 1 dari 1<br>
        Dicetak: {{ now()->format('d/m/Y H:i') }} WITA
    </div>
</div>

</body>
</html>
