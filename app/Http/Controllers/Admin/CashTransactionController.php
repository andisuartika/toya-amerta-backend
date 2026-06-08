<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use Illuminate\Http\Request;

class CashTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = CashTransaction::with('recordedBy')
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->when($request->date_from, fn ($q) => $q->where('transaction_date', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->where('transaction_date', '<=', $request->date_to))
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        $transactions = $query->limit(500)->get();

        $totalMasuk  = $transactions->where('type', 'masuk')->sum('amount');
        $totalKeluar = $transactions->where('type', 'keluar')->sum('amount');
        $saldo       = $totalMasuk - $totalKeluar;

        $categories = CashTransaction::categoryOptions();

        return view('admin.cash-transactions.index', compact(
            'transactions', 'totalMasuk', 'totalKeluar', 'saldo', 'categories'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'transaction_date' => 'required|date',
            'type'             => 'required|in:masuk,keluar',
            'category'         => 'required|string|max:100',
            'amount'           => 'required|numeric|min:1',
            'description'      => 'nullable|string',
        ]);

        // Jika kategori custom diisi, override category
        if ($data['category'] === '__custom__' && $request->filled('category_custom')) {
            $data['category'] = $request->category_custom;
        }

        CashTransaction::create(array_merge($data, ['recorded_by' => auth()->id()]));

        return back()->with('success', 'Transaksi kas berhasil ditambahkan.');
    }

    public function update(Request $request, CashTransaction $cashTransaction)
    {
        $data = $request->validate([
            'transaction_date' => 'required|date',
            'type'             => 'required|in:masuk,keluar',
            'category'         => 'required|string|max:100',
            'amount'           => 'required|numeric|min:1',
            'description'      => 'nullable|string',
        ]);

        if ($data['category'] === '__custom__' && $request->filled('category_custom')) {
            $data['category'] = $request->category_custom;
        }

        $cashTransaction->update($data);

        return back()->with('success', 'Transaksi kas berhasil diperbarui.');
    }

    public function destroy(CashTransaction $cashTransaction)
    {
        $cashTransaction->delete();
        return back()->with('success', 'Transaksi kas berhasil dihapus.');
    }
}
