<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\WaterReading;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WaterUsageReportController extends Controller
{
    private array $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $filters = $this->resolveFilters($request);
        $readings = $this->getReadings($filters);
        $summary  = $this->buildSummary($readings);
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $availableYears = WaterReading::selectRaw('DISTINCT period_year')
            ->orderBy('period_year', 'desc')->pluck('period_year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        return view('admin.water-usage-report.index', array_merge($filters, [
            'readings'       => $readings,
            'summary'        => $summary,
            'customers'      => $customers,
            'availableYears' => $availableYears,
            'monthNames'     => $this->monthNames,
        ]));
    }

    public function downloadExcel(Request $request): Response
    {
        $filters  = $this->resolveFilters($request);
        $readings = $this->getReadings($filters);
        $filename = $this->buildFilename($filters, 'xlsx');

        $spreadsheet = $this->buildSpreadsheet($filters, $readings);
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $filters  = $this->resolveFilters($request);
        $readings = $this->getReadings($filters);
        $summary  = $this->buildSummary($readings);
        $filename = $this->buildFilename($filters, 'pdf');

        $pdf = Pdf::loadView('admin.water-usage-report.pdf', array_merge($filters, [
            'readings'   => $readings,
            'summary'    => $summary,
            'monthNames' => $this->monthNames,
        ]))->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    // ── Private helpers ──────────────────────────────────────────────

    private function resolveFilters(Request $request): array
    {
        return [
            'year'        => (int) ($request->year ?? now()->year),
            'month'       => $request->month ? (int) $request->month : null,
            'customer_id' => $request->customer_id ? (int) $request->customer_id : null,
        ];
    }

    private function getReadings(array $filters)
    {
        return WaterReading::with(['customer.zone', 'customer.tariffRate'])
            ->whereYear('reading_date', $filters['year'])
            ->when($filters['month'],       fn ($q) => $q->where('period_month', $filters['month']))
            ->when($filters['customer_id'], fn ($q) => $q->where('customer_id', $filters['customer_id']))
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->orderBy('customer_id')
            ->get();
    }

    private function buildSummary($readings): array
    {
        $usageM3   = $readings->sum(fn ($r) => $r->current_reading - $r->previous_reading);
        $totalBill = $readings->sum('total_amount');
        $paid      = $readings->where('payment_status', 'paid')->sum('total_amount');
        $unpaid    = $totalBill - $paid;

        return [
            'total_readings' => $readings->count(),
            'usage_m3'       => $usageM3,
            'total_bill'     => $totalBill,
            'paid'           => $paid,
            'unpaid'         => $unpaid,
        ];
    }

    private function buildFilename(array $filters, string $ext): string
    {
        $parts = ['laporan-penggunaan-air'];
        if ($filters['customer_id']) {
            $c = Customer::find($filters['customer_id']);
            $parts[] = $c ? str($c->name)->slug()->toString() : $filters['customer_id'];
        }
        if ($filters['month']) {
            $parts[] = strtolower($this->monthNames[$filters['month']]);
        }
        $parts[] = $filters['year'];

        return implode('_', $parts) . '.' . $ext;
    }

    private function buildSpreadsheet(array $filters, $readings): Spreadsheet
    {
        $ss    = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Laporan Penggunaan Air');

        // ── Title ──
        $titleParts = ['Laporan Penggunaan Air — Toya Amerta'];
        if ($filters['month']) {
            $titleParts[] = ($this->monthNames[$filters['month']] . ' ' . $filters['year']);
        } else {
            $titleParts[] = 'Tahun ' . $filters['year'];
        }
        $title = implode(' · ', $titleParts);

        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'Lingkungan Sangket, Desa Buleleng — Dicetak: ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['rgb' => '546E7A']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // ── Header row ──
        $headers = [
            'A' => 'No.',
            'B' => 'No. Pelanggan',
            'C' => 'Nama Pelanggan',
            'D' => 'Zona',
            'E' => 'Periode',
            'F' => 'Meter Awal (m³)',
            'G' => 'Meter Akhir (m³)',
            'H' => 'Pemakaian (m³)',
            'I' => 'Total Tagihan (Rp)',
            'J' => 'Status',
        ];

        $row = 4;
        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}{$row}", $label);
        }
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BBDEFB']]],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(20);

        // ── Data rows ──
        $no = 1;
        foreach ($readings as $r) {
            $row++;
            $usage = $r->current_reading - $r->previous_reading;
            $bg    = ($no % 2 === 0) ? 'F5F9FF' : 'FFFFFF';

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $r->customer->customer_number ?? '-');
            $sheet->setCellValue("C{$row}", $r->customer->name ?? '-');
            $sheet->setCellValue("D{$row}", $r->customer->zone->name ?? '-');
            $sheet->setCellValue("E{$row}", ($this->monthNames[$r->period_month] ?? $r->period_month) . ' ' . $r->period_year);
            $sheet->setCellValue("F{$row}", $r->previous_reading);
            $sheet->setCellValue("G{$row}", $r->current_reading);
            $sheet->setCellValue("H{$row}", $usage);
            $sheet->setCellValue("I{$row}", $r->total_amount);
            $sheet->setCellValue("J{$row}", $r->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas');

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
            ]);
            $sheet->getStyle("J{$row}")->getFont()->getColor()->setRGB(
                $r->payment_status === 'paid' ? '388E3C' : 'D32F2F'
            );
            $sheet->getStyle("J{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:J{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("F{$row}:I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0');
        }

        // ── Summary row ──
        if ($readings->count() > 0) {
            $row++;
            $totalUsage = $readings->sum(fn ($r) => $r->current_reading - $r->previous_reading);
            $totalBill  = $readings->sum('total_amount');

            $sheet->mergeCells("A{$row}:E{$row}");
            $sheet->setCellValue("A{$row}", 'TOTAL');
            $sheet->setCellValue("H{$row}", $totalUsage);
            $sheet->setCellValue("I{$row}", $totalBill);

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font'    => ['bold' => true, 'color' => ['rgb' => '1565C0']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1565C0']]],
            ]);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$row}:I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0');
        }

        // ── Column widths ──
        $widths = ['A' => 5, 'B' => 14, 'C' => 28, 'D' => 16, 'E' => 16,
                   'F' => 16, 'G' => 16, 'H' => 16, 'I' => 18, 'J' => 14];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->getStyle("A4:J{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A1:J{$row}")->getAlignment()->setWrapText(false);

        return $ss;
    }
}
