<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Contracts\PaymentRepositoryInterface;
use App\Domain\DTOs\Payment\PaymentDTO;
use App\Domain\UseCases\Payment\ConfirmPaymentUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Models\Customer;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private ConfirmPaymentUseCase    $confirmUseCase,
        private PaymentRepositoryInterface $repo,
    ) {}

    /** Daftar tagihan belum lunas — halaman konfirmasi bayar */
    public function index(Request $request): View
    {
        $filters = $request->only(['zone_id', 'month', 'year']);
        $filters = array_filter($filters, fn ($v) => $v !== null && $v !== '');

        $unpaid = $this->repo->unpaid($filters);
        $zones  = Zone::where('is_active', true)->orderBy('name')->get();

        return view('admin.payments.index', compact('unpaid', 'zones', 'filters'));
    }

    /** Simpan konfirmasi pembayaran */
    public function store(PaymentRequest $request): RedirectResponse
    {
        try {
            $this->confirmUseCase->execute(
                PaymentDTO::fromArray($request->validated()),
                auth()->id()
            );
            return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }
    }

    /** Batalkan / hapus pembayaran */
    public function destroy(int $payment): RedirectResponse
    {
        try {
            $this->repo->delete($payment);
            return back()->with('success', 'Pembayaran berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan pembayaran.');
        }
    }

    /** Riwayat semua pembayaran */
    public function history(Request $request): View
    {
        $filters   = $request->only(['customer_id', 'date_from', 'date_to']);
        $filters   = array_filter($filters, fn ($v) => $v !== null && $v !== '');
        $payments  = $this->repo->history($filters);
        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        return view('admin.payments.history', compact('payments', 'customers', 'filters'));
    }
}
