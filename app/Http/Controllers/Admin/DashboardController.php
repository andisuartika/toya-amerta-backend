<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\Maintenance;
use App\Models\PdamBill;
use App\Models\PaymentRecord;
use App\Models\TariffRate;
use App\Models\User;
use App\Models\WaterReading;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now   = now();
        $month = $now->month;
        $year  = $now->year;

        // ── Statistik umum ─────────────────────────────────────────
        $stats = [
            'total_customers' => Customer::where('is_active', true)->count(),
            'total_zones'     => Zone::where('is_active', true)->count(),
            'total_tariffs'   => TariffRate::where('is_active', true)->count(),
            'total_officers'  => User::where('role', 'petugas')->where('is_active', true)->count(),
        ];

        // ── Kas bulan ini ──────────────────────────────────────────
        $kasbulan = CashTransaction::selectRaw("
                SUM(CASE WHEN type='masuk'  THEN amount ELSE 0 END) AS masuk,
                SUM(CASE WHEN type='keluar' THEN amount ELSE 0 END) AS keluar
            ")
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->first();

        // ── Saldo kas keseluruhan ──────────────────────────────────
        $saldoKas = CashTransaction::selectRaw(
            "SUM(CASE WHEN type='masuk' THEN amount ELSE -amount END) AS saldo"
        )->value('saldo') ?? 0;

        // ── Tagihan bulan ini ──────────────────────────────────────
        $tagihanBulanIni = WaterReading::where('period_year', $year)
            ->where('period_month', $month)
            ->selectRaw("
                COUNT(*) AS total,
                SUM(CASE WHEN payment_status='lunas' THEN 1 ELSE 0 END) AS lunas,
                SUM(CASE WHEN payment_status='belum_bayar' THEN 1 ELSE 0 END) AS belum_bayar,
                SUM(CASE WHEN payment_status='sebagian' THEN 1 ELSE 0 END) AS sebagian,
                SUM(total_amount) AS total_amount,
                SUM(CASE WHEN payment_status='lunas' THEN total_amount ELSE 0 END) AS terkumpul
            ")->first();

        // ── Maintenance aktif ──────────────────────────────────────
        $maintenanceAktif = Maintenance::whereIn('status', ['dilaporkan', 'dalam_proses'])
            ->selectRaw("
                COUNT(*) AS total,
                SUM(CASE WHEN priority='darurat' THEN 1 ELSE 0 END) AS darurat,
                SUM(CASE WHEN priority='tinggi'  THEN 1 ELSE 0 END) AS tinggi
            ")->first();

        // ── Tagihan PDAM pusat belum lunas ─────────────────────────
        $pdamBelumLunas = PdamBill::where('payment_status', 'belum_bayar')
            ->orderByDesc('due_date')->get();

        // ── Grafik tren 6 bulan (masuk vs keluar) ─────────────────
        $trenData = CashTransaction::selectRaw("
                YEAR(transaction_date) AS y,
                MONTH(transaction_date) AS m,
                SUM(CASE WHEN type='masuk'  THEN amount ELSE 0 END) AS masuk,
                SUM(CASE WHEN type='keluar' THEN amount ELSE 0 END) AS keluar
            ")
            ->where('transaction_date', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->groupByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->orderByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->get();

        $monthNames = [
            1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun',
            7=>'Jul', 8=>'Agt', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des',
        ];

        // Pastikan semua 6 bulan terisi (0 kalau belum ada data)
        $trenLabels = [];
        $trenMasuk  = [];
        $trenKeluar = [];
        for ($i = 5; $i >= 0; $i--) {
            $d   = $now->copy()->subMonths($i);
            $key = $d->year . '-' . $d->month;
            $row = $trenData->first(fn ($r) => $r->y == $d->year && $r->m == $d->month);
            $trenLabels[] = $monthNames[$d->month] . ' ' . $d->year;
            $trenMasuk[]  = (int) ($row?->masuk  ?? 0);
            $trenKeluar[] = (int) ($row?->keluar ?? 0);
        }

        // ── 5 transaksi kas terbaru ────────────────────────────────
        $transaksiTerbaru = CashTransaction::with('recordedBy')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // ── Pembayaran terbaru ─────────────────────────────────────
        $pembayaranTerbaru = PaymentRecord::with(['customer', 'waterReading'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats', 'kasbulan', 'saldoKas',
            'tagihanBulanIni', 'maintenanceAktif',
            'pdamBelumLunas', 'transaksiTerbaru', 'pembayaranTerbaru',
            'trenLabels', 'trenMasuk', 'trenKeluar',
            'monthNames', 'month', 'year'
        ));
    }
}
