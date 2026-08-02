<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Contracts\CustomerRepositoryInterface;
use App\Domain\Contracts\MeterReplacementRepositoryInterface;
use App\Domain\Contracts\WaterReadingRepositoryInterface;
use App\Domain\Contracts\ZoneRepositoryInterface;
use App\Domain\DTOs\Customer\CustomerDTO;
use App\Domain\DTOs\MeterReplacement\MeterReplacementDTO;
use App\Domain\UseCases\Customer\CreateCustomerUseCase;
use App\Domain\UseCases\Customer\DeleteCustomerUseCase;
use App\Domain\UseCases\Customer\UpdateCustomerUseCase;
use App\Domain\UseCases\MeterReplacement\ReplaceMeterUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Http\Requests\Admin\MeterReplacementRequest;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\NewCustomerFee;
use App\Models\TariffRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private CreateCustomerUseCase              $createUseCase,
        private UpdateCustomerUseCase              $updateUseCase,
        private DeleteCustomerUseCase              $deleteUseCase,
        private ReplaceMeterUseCase                $replaceMeterUseCase,
        private ZoneRepositoryInterface            $zoneRepo,
        private CustomerRepositoryInterface        $customerRepo,
        private WaterReadingRepositoryInterface    $waterReadingRepo,
        private MeterReplacementRepositoryInterface $meterReplacementRepo,
    ) {}

    public function index(Request $request): View
    {
        $zones   = $this->zoneRepo->allActive();
        $tariffs = TariffRate::where('is_active', true)->orderBy('name')->get();

        $customers = Customer::with(['zone', 'tariffRate'])
            ->when($request->zone_id, fn ($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->status !== null && $request->status !== '',
                fn ($q) => $q->where('is_active', $request->status)
            )
            ->orderBy('name')
            ->get();

        return view('admin.customers.index', compact('customers', 'zones', 'tariffs'));
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['customer_number'] = $data['customer_number'] ?: $this->customerRepo->generateCustomerNumber();
        $customer = $this->createUseCase->execute(CustomerDTO::fromArray($data));

        // Simpan biaya pemasangan jika diisi
        $feeAmount = (int) str_replace('.', '', $request->input('fee_amount', 0));
        if ($feeAmount > 0) {
            $fee = NewCustomerFee::create([
                'customer_id'      => $customer->id,
                'fee_type'         => $request->fee_type ?? 'biaya_instalasi',
                'amount'           => $feeAmount,
                'payment_date'     => $request->fee_payment_date ?? now()->toDateString(),
                'payment_method'   => $request->fee_payment_method ?? 'tunai',
                'description'      => $request->fee_description,
                'recorded_by'      => auth()->id(),
                'receipt_number'   => 'BPS-' . date('Ymd') . '-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
            ]);

            // Auto-record ke kas masuk
            $kas = CashTransaction::create([
                'transaction_date' => $fee->payment_date,
                'type'             => 'masuk',
                'category'         => 'Biaya Pemasangan Pelanggan Baru',
                'amount'           => $feeAmount,
                'description'      => \App\Models\NewCustomerFee::feeTypeLabel($fee->fee_type) .
                                      ' — ' . $customer->name .
                                      ' (' . $fee->receipt_number . ')',
                'reference_type'   => 'new_customer_fees',
                'reference_id'     => $fee->id,
                'recorded_by'      => auth()->id(),
            ]);
            $fee->update(['cash_transaction_id' => $kas->id]);
        }

        return back()->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(CustomerRequest $request, int $customer): RedirectResponse
    {
        $this->updateUseCase->execute($customer, CustomerDTO::fromArray($request->validated()));
        return back()->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(int $customer): RedirectResponse
    {
        $this->deleteUseCase->execute($customer);
        return back()->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function generateNumber(): JsonResponse
    {
        return response()->json(['number' => $this->customerRepo->generateCustomerNumber()]);
    }

    /** AJAX: info meteran pelanggan untuk modal Ganti Meteran (angka acuan saat ini + riwayat) */
    public function meterInfo(int $customer): JsonResponse
    {
        $this->customerRepo->findById($customer);

        return response()->json([
            'current_reading' => $this->waterReadingRepo->baselineReading($customer),
            'history'         => $this->meterReplacementRepo->forCustomer($customer)->map(fn ($r) => [
                'replaced_at'        => $r->replaced_at->format('d/m/Y'),
                'old_reading_final'  => $r->old_reading_final,
                'new_reading_start'  => $r->new_reading_start,
                'reason'             => $r->reason,
                'notes'              => $r->notes,
                'recorded_by'        => $r->recordedBy?->name,
            ])->values(),
        ]);
    }

    public function replaceMeter(MeterReplacementRequest $request, int $customer): RedirectResponse
    {
        $this->customerRepo->findById($customer);

        $data = $request->validated();
        $data['customer_id'] = $customer;
        $data['recorded_by'] = auth()->id();

        $this->replaceMeterUseCase->execute(MeterReplacementDTO::fromArray($data));

        return back()->with('success', 'Penggantian meteran berhasil dicatat. Pencatatan periode berikutnya akan memakai angka meteran baru.');
    }
}
