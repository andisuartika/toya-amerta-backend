<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinancialReportController extends Controller
{
    public function index(Request $request): View
    {
        $year  = (int) ($request->year  ?? now()->year);
        $month = $request->month ?? null; // null = tampilkan semua bulan (ringkasan tahunan)

        // Ringkasan per bulan untuk tahun dipilih
        $monthly = CashTransaction::selectRaw("
                MONTH(transaction_date) AS month,
                SUM(CASE WHEN type='masuk'  THEN amount ELSE 0 END) AS total_masuk,
                SUM(CASE WHEN type='keluar' THEN amount ELSE 0 END) AS total_keluar,
                SUM(CASE WHEN type='masuk'  THEN amount ELSE -amount END) AS saldo
            ")
            ->whereYear('transaction_date', $year)
            ->groupByRaw('MONTH(transaction_date)')
            ->orderByRaw('MONTH(transaction_date)')
            ->get()
            ->keyBy('month');

        // Ringkasan per kategori (untuk bulan tertentu atau seluruh tahun)
        $byCategory = CashTransaction::selectRaw("
                type, category,
                SUM(amount) AS total,
                COUNT(*) AS jumlah
            ")
            ->whereYear('transaction_date', $year)
            ->when($month, fn ($q) => $q->whereMonth('transaction_date', $month))
            ->groupBy('type', 'category')
            ->orderBy('type')
            ->orderByDesc('total')
            ->get();

        // Total keseluruhan tahun
        $yearSummary = CashTransaction::selectRaw("
                SUM(CASE WHEN type='masuk'  THEN amount ELSE 0 END) AS total_masuk,
                SUM(CASE WHEN type='keluar' THEN amount ELSE 0 END) AS total_keluar,
                SUM(CASE WHEN type='masuk'  THEN amount ELSE -amount END) AS saldo
            ")
            ->whereYear('transaction_date', $year)
            ->when($month, fn ($q) => $q->whereMonth('transaction_date', $month))
            ->first();

        // Daftar transaksi detail (untuk bulan tertentu)
        $transactions = collect();
        if ($month) {
            $transactions = CashTransaction::with('recordedBy')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->orderBy('transaction_date')
                ->orderByDesc('id')
                ->get();
        }

        // Tahun-tahun yang tersedia
        $availableYears = CashTransaction::selectRaw('YEAR(transaction_date) AS y')
            ->groupByRaw('YEAR(transaction_date)')
            ->orderByRaw('YEAR(transaction_date) DESC')
            ->pluck('y');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        $monthNames = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];

        return view('admin.financial-report.index', compact(
            'year', 'month', 'monthly', 'byCategory',
            'yearSummary', 'transactions', 'availableYears', 'monthNames'
        ));
    }
}
