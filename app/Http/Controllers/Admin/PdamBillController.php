<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use App\Models\PdamBill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PdamBillController extends Controller
{
    public function index(Request $request): View
    {
        $year = $request->year ?? now()->year;

        $bills = PdamBill::with('createdBy')
            ->when($year, fn ($q) => $q->where('period_year', $year))
            ->when($request->status, fn ($q) => $q->where('payment_status', $request->status))
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->get();

        $summary = [
            'total_tagihan' => $bills->sum('total_bill_amount'),
            'belum_bayar'   => $bills->where('payment_status', 'belum_bayar')->sum('total_bill_amount'),
            'lunas'         => $bills->where('payment_status', 'lunas')->sum('total_bill_amount'),
            'jatuh_tempo'   => $bills->filter(fn ($b) => $b->is_overdue)->count(),
        ];

        $availableYears = PdamBill::selectRaw('period_year')->groupBy('period_year')
            ->orderByDesc('period_year')->pluck('period_year');
        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        $monthNames = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];

        return view('admin.pdam-bills.index', compact(
            'bills', 'summary', 'year', 'availableYears', 'monthNames'
        ));
    }

    private function parseTiers(Request $request): array
    {
        $tiers     = [];
        $totalM3   = 0;
        $baseAmount = 0;

        $labels  = $request->input('tier_label', []);
        $volumes = $request->input('tier_volume', []);
        $prices  = $request->input('tier_price', []);

        foreach ($labels as $i => $label) {
            $vol   = (float) str_replace('.', '', $volumes[$i] ?? 0);
            $price = (float) str_replace('.', '', $prices[$i]  ?? 0);
            if ($vol <= 0) continue;
            $subtotal   = $vol * $price;
            $totalM3   += $vol;
            $baseAmount += $subtotal;
            $tiers[] = [
                'label'    => trim($label) ?: ('Tier ' . ($i + 1)),
                'volume'   => $vol,
                'price'    => $price,
                'subtotal' => $subtotal,
            ];
        }

        return [$tiers, $totalM3, $baseAmount];
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'period_year'       => 'required|integer|min:2000|max:2100',
            'period_month'      => 'required|integer|min:1|max:12',
            'tier_volume'       => 'required|array|min:1',
            'tier_volume.*'     => 'nullable|numeric|min:0',
            'tier_price'        => 'required|array|min:1',
            'additional_charge' => 'nullable|numeric|min:0',
            'total_bill_amount' => 'required|numeric|min:1',
            'bill_date'         => 'required|date',
            'due_date'          => 'required|date|after_or_equal:bill_date',
            'invoice_number'    => 'nullable|string|max:50|unique:pdam_bills,invoice_number',
            'notes'             => 'nullable|string|max:500',
        ], [
            'tier_volume.required'      => 'Minimal 1 tier volume harus diisi.',
            'total_bill_amount.required'=> 'Total tagihan wajib diisi.',
            'bill_date.required'        => 'Tanggal tagihan wajib diisi.',
            'due_date.required'         => 'Tanggal jatuh tempo wajib diisi.',
            'due_date.after_or_equal'   => 'Jatuh tempo tidak boleh sebelum tanggal tagihan.',
            'invoice_number.unique'     => 'Nomor invoice sudah digunakan.',
        ]);

        $existing = PdamBill::where('period_year', $request->period_year)
            ->where('period_month', $request->period_month)->first();
        if ($existing) {
            return back()->withErrors(['period_month' => 'Tagihan periode ini sudah ada.'])->withInput();
        }

        [$tiers, $totalM3, $baseAmount] = $this->parseTiers($request);

        if (empty($tiers)) {
            return back()->withErrors(['tier_volume' => 'Minimal 1 tier harus memiliki volume > 0.'])->withInput();
        }

        // pdam_price_per_m3 diisi weighted average (untuk kompatibilitas kolom generated)
        $avgPrice = $totalM3 > 0 ? round($baseAmount / $totalM3, 2) : 0;

        PdamBill::create([
            'period_year'        => $request->period_year,
            'period_month'       => $request->period_month,
            'total_m3_from_pdam' => $totalM3,
            'pdam_price_per_m3'  => $avgPrice,
            'additional_charge'  => (float) str_replace('.', '', $request->additional_charge ?? 0),
            'total_bill_amount'  => (float) str_replace('.', '', $request->total_bill_amount),
            'bill_date'          => $request->bill_date,
            'due_date'           => $request->due_date,
            'invoice_number'     => $request->invoice_number,
            'notes'              => $request->notes,
            'tier_details'       => $tiers,
            'created_by'         => auth()->id(),
        ]);

        return back()->with('success', 'Tagihan PDAM Pusat berhasil ditambahkan.');
    }

    public function update(Request $request, PdamBill $pdamBill): RedirectResponse
    {
        $request->validate([
            'tier_volume'       => 'required|array|min:1',
            'tier_volume.*'     => 'nullable|numeric|min:0',
            'additional_charge' => 'nullable|numeric|min:0',
            'total_bill_amount' => 'required|numeric|min:1',
            'bill_date'         => 'required|date',
            'due_date'          => 'required|date|after_or_equal:bill_date',
            'invoice_number'    => 'nullable|string|max:50|unique:pdam_bills,invoice_number,' . $pdamBill->id,
            'notes'             => 'nullable|string|max:500',
        ], [
            'total_bill_amount.required' => 'Total tagihan wajib diisi.',
            'due_date.after_or_equal'    => 'Jatuh tempo tidak boleh sebelum tanggal tagihan.',
            'invoice_number.unique'      => 'Nomor invoice sudah digunakan.',
        ]);

        [$tiers, $totalM3, $baseAmount] = $this->parseTiers($request);

        if (empty($tiers)) {
            return back()->withErrors(['tier_volume' => 'Minimal 1 tier harus memiliki volume > 0.'])->withInput();
        }

        $avgPrice = $totalM3 > 0 ? round($baseAmount / $totalM3, 2) : 0;

        $pdamBill->update([
            'total_m3_from_pdam' => $totalM3,
            'pdam_price_per_m3'  => $avgPrice,
            'additional_charge'  => (float) str_replace('.', '', $request->additional_charge ?? 0),
            'total_bill_amount'  => (float) str_replace('.', '', $request->total_bill_amount),
            'bill_date'          => $request->bill_date,
            'due_date'           => $request->due_date,
            'invoice_number'     => $request->invoice_number,
            'notes'              => $request->notes,
            'tier_details'       => $tiers,
        ]);

        return back()->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function confirm(Request $request, PdamBill $pdamBill): RedirectResponse
    {
        if ($pdamBill->payment_status === 'lunas') {
            return back()->withErrors(['status' => 'Tagihan sudah lunas.']);
        }

        $data = $request->validate([
            'payment_date' => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($pdamBill, $data) {
            $pdamBill->update([
                'payment_status' => 'lunas',
                'payment_date'   => $data['payment_date'],
                'notes'          => $data['notes'] ?? $pdamBill->notes,
            ]);

            // Auto-record ke kas keluar
            $kas = CashTransaction::create([
                'transaction_date' => $data['payment_date'],
                'type'             => 'keluar',
                'category'         => 'Tagihan PDAM Pusat',
                'amount'           => $pdamBill->total_bill_amount,
                'description'      => 'Tagihan PDAM Pusat ' . $pdamBill->period_label .
                                      ($pdamBill->invoice_number ? ' (' . $pdamBill->invoice_number . ')' : ''),
                'reference_type'   => 'pdam_bills',
                'reference_id'     => $pdamBill->id,
                'recorded_by'      => auth()->id(),
            ]);
            $pdamBill->update(['cash_transaction_id' => $kas->id]);
        });

        return back()->with('success', 'Tagihan berhasil dikonfirmasi lunas dan dicatat ke kas.');
    }

    public function destroy(PdamBill $pdamBill): RedirectResponse
    {
        DB::transaction(function () use ($pdamBill) {
            if ($pdamBill->cash_transaction_id) {
                CashTransaction::find($pdamBill->cash_transaction_id)?->delete();
            }
            $pdamBill->delete();
        });

        return back()->with('success', 'Tagihan berhasil dihapus.');
    }
}
