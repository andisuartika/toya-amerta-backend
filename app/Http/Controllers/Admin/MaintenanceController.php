<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Contracts\MaintenanceRepositoryInterface;
use App\Domain\Contracts\ZoneRepositoryInterface;
use App\Domain\DTOs\Maintenance\MaintenanceDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function __construct(
        private MaintenanceRepositoryInterface $repo,
        private ZoneRepositoryInterface $zoneRepo,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'priority', 'category', 'zone_id']);
        $maintenances = $this->repo->all($filters);
        $zones = $this->zoneRepo->allActive();

        $summary = [
            'dilaporkan'   => $maintenances->where('status', 'dilaporkan')->count(),
            'dalam_proses' => $maintenances->where('status', 'dalam_proses')->count(),
            'selesai'      => $maintenances->where('status', 'selesai')->count(),
            'ditunda'      => $maintenances->where('status', 'ditunda')->count(),
        ];

        return view('admin.maintenances.index', compact('maintenances', 'zones', 'filters', 'summary'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:200',
            'location'      => 'required|string|max:255',
            'category'      => 'required|in:pipa_bocor,meteran_rusak,pompa,reservoir,instalasi_baru,lainnya',
            'priority'      => 'required|in:rendah,sedang,tinggi,darurat',
            'reported_date' => 'required|date',
            'zone_id'       => 'nullable|exists:zones,id',
            'customer_id'   => 'nullable|exists:customers,id',
            'description'   => 'nullable|string',
            'material_cost' => 'nullable|numeric|min:0',
            'labor_cost'    => 'nullable|numeric|min:0',
        ]);

        $this->repo->create(MaintenanceDTO::fromArray($data), auth()->id());

        return back()->with('success', 'Laporan maintenance berhasil ditambahkan.');
    }

    public function update(Request $request, int $maintenance)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:200',
            'location'      => 'required|string|max:255',
            'category'      => 'required|in:pipa_bocor,meteran_rusak,pompa,reservoir,instalasi_baru,lainnya',
            'priority'      => 'required|in:rendah,sedang,tinggi,darurat',
            'reported_date' => 'required|date',
            'zone_id'       => 'nullable|exists:zones,id',
            'customer_id'   => 'nullable|exists:customers,id',
            'description'   => 'nullable|string',
            'material_cost' => 'nullable|numeric|min:0',
            'labor_cost'    => 'nullable|numeric|min:0',
        ]);

        $this->repo->update($maintenance, $data);

        return back()->with('success', 'Data maintenance berhasil diperbarui.');
    }

    public function updateStatus(Request $request, int $maintenance)
    {
        $data = $request->validate([
            'status'          => 'required|in:dilaporkan,dalam_proses,selesai,ditunda',
            'handled_date'    => 'nullable|date',
            'completed_date'  => 'nullable|date',
            'material_cost'   => 'nullable|numeric|min:0',
            'labor_cost'      => 'nullable|numeric|min:0',
            'description'     => 'nullable|string',
        ]);

        $this->repo->updateStatus($maintenance, $data['status'], array_filter($data, fn ($v) => $v !== null));

        return back()->with('success', 'Status maintenance berhasil diperbarui.');
    }

    public function destroy(int $maintenance)
    {
        $this->repo->delete($maintenance);
        return back()->with('success', 'Data maintenance berhasil dihapus.');
    }
}
