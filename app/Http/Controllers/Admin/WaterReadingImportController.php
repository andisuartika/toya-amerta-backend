<?php

namespace App\Http\Controllers\Admin;

use App\Domain\DTOs\WaterReading\ImportReadingDTO;
use App\Domain\UseCases\WaterReading\ImportWaterReadingRowUseCase;
use App\Http\Controllers\Controller;
use App\Infrastructure\Import\CustomerNameMatcher;
use App\Infrastructure\Import\WaterReadingExcelParser;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\PdamBill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WaterReadingImportController extends Controller
{
    public function __construct(
        private WaterReadingExcelParser $parser,
        private CustomerNameMatcher $matcher,
        private ImportWaterReadingRowUseCase $importRow,
    ) {}

    public function create(): View
    {
        return view('admin.water-readings.import.upload');
    }

    public function template(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        $sheet->fromArray(['PELANGGAN', 'METER AWAL', 'METER AKHIR', 'HARGA/M3', 'TOTAL'], null, 'A1');
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9E1F2');

        $sheet->fromArray([
            ['Contoh: Made Sedana Yasa', 419, 426, 8000, 56000],
            ['Contoh: Made Berata',       86,  88, 8000, 16000],
            ['Contoh: Putu Sedana',     1838, 1842, 8000, 32000],
        ], null, 'A2');

        $sheet->fromArray(['TOTAL', '', '', '', '=SUM(E2:E4)'], null, 'A5');
        $sheet->getStyle('A5:E5')->getFont()->setBold(true);

        $sheet->fromArray(['', '', 'BIAYA ADMIN', '', 50000], null, 'A6');
        $sheet->fromArray(['', '', 'GRAND TOTAL', '', '=E5+E6'], null, 'A7');
        $sheet->getStyle('A7:E7')->getFont()->setBold(true);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template-import-pencatatan-meter.xlsx"',
        ]);
    }

    public function preview(Request $request): View|RedirectResponse
    {
        $request->validate([
            'year'         => 'required|integer|min:2000|max:2100',
            'month'        => 'required|integer|min:1|max:12',
            'file'         => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'biaya_admin'  => 'nullable|numeric|min:0',
            'pdam_payment' => 'nullable|numeric|min:0',
        ]);

        $year        = (int) $request->year;
        $month       = (int) $request->month;
        $biayaAdmin  = $request->filled('biaya_admin') ? (float) $request->biaya_admin : null;
        $pdamPayment = $request->filled('pdam_payment') ? (float) $request->pdam_payment : null;

        $parsedRows = $this->parser->parse($request->file('file')->getRealPath());

        if (empty($parsedRows)) {
            return back()->with('error', 'Tidak ada baris data yang berhasil dibaca dari file. Pastikan format kolom sesuai (PELANGGAN, METER AWAL, METER AKHIR, HARGA/M3, TOTAL).');
        }

        $customers = Customer::orderBy('name')->get();

        $rows = array_map(function (array $row) use ($customers, $year, $month) {
            $matched = $this->matcher->match($row['name'], $customers);

            return [
                'name'             => $row['name'],
                'previous_reading' => $row['previous_reading'],
                'current_reading'  => $row['current_reading'],
                'usage_m3'         => round($row['current_reading'] - $row['previous_reading'], 2),
                'price_per_m3'     => $row['price_per_m3'],
                'total_amount'     => $row['total_amount'],
                'matched_customer_id' => $matched?->id,
                'suggestions'      => $matched ? collect() : $this->matcher->suggest($row['name'], $customers),
                'already_recorded' => $matched
                    ? \App\Models\WaterReading::where('customer_id', $matched->id)
                        ->where('period_year', $year)
                        ->where('period_month', $month)
                        ->exists()
                    : false,
            ];
        }, $parsedRows);

        return view('admin.water-readings.import.review', [
            'rows'        => $rows,
            'customers'   => $customers,
            'year'        => $year,
            'month'       => $month,
            'biayaAdmin'  => $biayaAdmin,
            'pdamPayment' => $pdamPayment,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year'                       => 'required|integer|min:2000|max:2100',
            'month'                      => 'required|integer|min:1|max:12',
            'biaya_admin'                => 'nullable|numeric|min:0',
            'pdam_payment'               => 'nullable|numeric|min:0',
            'rows'                       => 'required|array',
            'rows.*.name'                => 'required|string',
            'rows.*.previous_reading'    => 'required|numeric',
            'rows.*.current_reading'     => 'required|numeric',
            'rows.*.price_per_m3'        => 'required|numeric|min:0',
            'rows.*.total_amount'        => 'required|numeric',
            'rows.*.include'             => 'nullable|boolean',
            'rows.*.customer_id'         => 'nullable|integer|exists:customers,id',
            'rows.*.payment_status'      => 'required|in:belum_bayar,sebagian,lunas',
            'rows.*.amount_paid'         => 'nullable|numeric|min:0',
        ]);

        $year       = (int) $validated['year'];
        $month      = (int) $validated['month'];
        $readingDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $created = 0;
        $skipped = 0;
        $failed  = [];

        foreach ($validated['rows'] as $row) {
            $include    = (bool) ($row['include'] ?? false);
            $customerId = $row['customer_id'] ?? null;

            if (! $include || ! $customerId) {
                $skipped++;
                continue;
            }

            $dto = new ImportReadingDTO(
                customer_id:      (int) $customerId,
                officer_id:       auth()->id(),
                period_year:      $year,
                period_month:     $month,
                previous_reading: (float) $row['previous_reading'],
                current_reading:  (float) $row['current_reading'],
                price_per_m3:     (float) $row['price_per_m3'],
                total_amount:     (float) $row['total_amount'],
                reading_date:     $readingDate,
                payment_status:   $row['payment_status'],
                amount_paid:      isset($row['amount_paid']) ? (float) $row['amount_paid'] : null,
            );

            try {
                $result = $this->importRow->execute($dto, auth()->id());

                if ($result['status'] === 'created') {
                    $created++;
                } else {
                    $skipped++;
                }
            } catch (\Throwable $e) {
                $failed[] = $row['name'] . ': ' . $e->getMessage();
            }
        }

        $periodLabel = Carbon::create($year, $month)->translatedFormat('F Y');

        if (! empty($validated['biaya_admin']) && $validated['biaya_admin'] > 0) {
            CashTransaction::create([
                'transaction_date' => $readingDate,
                'type'             => 'keluar',
                'category'         => 'Biaya Administrasi',
                'amount'           => $validated['biaya_admin'],
                'description'      => 'Biaya admin impor data pencatatan periode ' . $periodLabel,
                'recorded_by'      => auth()->id(),
            ]);
        }

        $pdamSkipped = false;

        if (! empty($validated['pdam_payment']) && $validated['pdam_payment'] > 0) {
            $existingPdamBill = PdamBill::where('period_year', $year)
                ->where('period_month', $month)
                ->first();

            if ($existingPdamBill) {
                $pdamSkipped = true;
            } else {
                $pdamBill = PdamBill::create([
                    'period_year'        => $year,
                    'period_month'       => $month,
                    'total_m3_from_pdam' => 0,
                    'pdam_price_per_m3'  => 0,
                    'additional_charge'  => 0,
                    'total_bill_amount'  => $validated['pdam_payment'],
                    'bill_date'          => $readingDate,
                    'due_date'           => $readingDate,
                    'payment_status'     => 'lunas',
                    'payment_date'       => $readingDate,
                    'notes'              => 'Diimpor dari data historis',
                    'created_by'         => auth()->id(),
                ]);

                $kas = CashTransaction::create([
                    'transaction_date' => $readingDate,
                    'type'             => 'keluar',
                    'category'         => 'Tagihan PDAM Pusat',
                    'amount'           => $validated['pdam_payment'],
                    'description'      => 'Tagihan PDAM Pusat ' . $periodLabel . ' (diimpor dari data historis)',
                    'reference_type'   => 'pdam_bills',
                    'reference_id'     => $pdamBill->id,
                    'recorded_by'      => auth()->id(),
                ]);

                $pdamBill->update(['cash_transaction_id' => $kas->id]);
            }
        }

        $message = "Impor selesai: {$created} data tersimpan, {$skipped} baris dilewati.";

        if ($pdamSkipped) {
            $message .= ' Tagihan PDAM Pusat untuk periode ini sudah ada sebelumnya, dilewati.';
        }

        if (! empty($failed)) {
            $message .= ' Gagal: ' . implode('; ', $failed);
        }

        return redirect()
            ->route('admin.water-readings.import.create')
            ->with($failed ? 'error' : 'success', $message);
    }
}
