<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\WaterReading;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerCheckController extends Controller
{
    public function index(): View
    {
        return view('public.cek.index');
    }

    public function search(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_number' => ['required', 'string'],
        ], [
            'customer_number.required' => 'Silakan masukkan nomor pelanggan.',
        ]);

        $customer = Customer::where('customer_number', trim($request->customer_number))->first();

        if (! $customer) {
            return back()->withInput()->with('error', 'Nomor pelanggan tidak ditemukan. Periksa kembali nomor Anda.');
        }

        return redirect()->route('public.cek.history', $customer->customer_number);
    }

    public function history(string $customerNumber): View
    {
        $customer = Customer::with(['zone', 'tariffRate'])
            ->where('customer_number', $customerNumber)
            ->firstOrFail();

        $readings = WaterReading::where('customer_id', $customer->id)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->limit(24)
            ->get();

        return view('public.cek.history', compact('customer', 'readings'));
    }

    public function detail(string $customerNumber, int $reading): View
    {
        $customer = Customer::where('customer_number', $customerNumber)->firstOrFail();

        $waterReading = WaterReading::with('paymentRecords')
            ->where('customer_id', $customer->id)
            ->where('id', $reading)
            ->firstOrFail();

        return view('public.cek.detail', compact('customer', 'waterReading'));
    }
}
