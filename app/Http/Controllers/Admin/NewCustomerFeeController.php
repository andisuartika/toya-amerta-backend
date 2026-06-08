<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\NewCustomerFee;
use Illuminate\Http\Request;

class NewCustomerFeeController extends Controller
{
    public function index(Request $request)
    {
        $fees = NewCustomerFee::with(['customer.zone', 'recordedBy'])
            ->when($request->customer_id, fn ($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->fee_type, fn ($q) => $q->where('fee_type', $request->fee_type))
            ->when($request->date_from, fn ($q) => $q->where('payment_date', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->where('payment_date', '<=', $request->date_to))
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $customers = Customer::orderBy('name')->get();
        $total     = $fees->sum('amount');

        return view('admin.new-customer-fees.index', compact('fees', 'customers', 'total'));
    }

    public function destroy(NewCustomerFee $newCustomerFee)
    {
        $newCustomerFee->delete();
        return back()->with('success', 'Data biaya pemasangan berhasil dihapus.');
    }
}
