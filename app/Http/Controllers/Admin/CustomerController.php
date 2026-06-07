<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Contracts\CustomerRepositoryInterface;
use App\Domain\Contracts\ZoneRepositoryInterface;
use App\Domain\DTOs\Customer\CustomerDTO;
use App\Domain\UseCases\Customer\CreateCustomerUseCase;
use App\Domain\UseCases\Customer\DeleteCustomerUseCase;
use App\Domain\UseCases\Customer\UpdateCustomerUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Models\Customer;
use App\Models\TariffRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private CreateCustomerUseCase       $createUseCase,
        private UpdateCustomerUseCase       $updateUseCase,
        private DeleteCustomerUseCase       $deleteUseCase,
        private ZoneRepositoryInterface     $zoneRepo,
        private CustomerRepositoryInterface $customerRepo,
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
        $this->createUseCase->execute(CustomerDTO::fromArray($data));
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
}
