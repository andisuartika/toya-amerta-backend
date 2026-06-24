<?php

namespace App\Infrastructure\Import;

use PhpOffice\PhpSpreadsheet\IOFactory;

class WaterReadingExcelParser
{
    /**
     * Membaca file Excel rekap pencatatan manual.
     * Format kolom: PELANGGAN, METER AWAL, METER AKHIR, HARGA/M3, TOTAL.
     * Baris header, baris kosong, dan baris ringkasan (TOTAL/GRAND TOTAL/BIAYA ADMIN) otomatis dilewati.
     *
     * @return array<int, array{name: string, previous_reading: float, current_reading: float, price_per_m3: float, total_amount: float}>
     */
    public function parse(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true);

        $skipLabels = ['TOTAL', 'GRAND TOTAL', 'BIAYA ADMIN', 'PEMBAYARAN PDAM', 'PELANGGAN'];
        $result     = [];

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue;
            }

            $name = trim((string) ($row['A'] ?? ''));

            if ($name === '' || in_array(mb_strtoupper($name), $skipLabels, true)) {
                continue;
            }

            $previous = $row['B'] ?? null;
            $current  = $row['C'] ?? null;

            if (! is_numeric($previous) || ! is_numeric($current)) {
                continue;
            }

            $priceRaw = (string) ($row['D'] ?? '0');
            $price    = (float) preg_replace('/[^0-9.]/', '', $priceRaw);

            $totalRaw = (string) ($row['E'] ?? '0');
            $total    = (float) preg_replace('/[^0-9.]/', '', $totalRaw);

            $result[] = [
                'name'             => $name,
                'previous_reading' => (float) $previous,
                'current_reading'  => (float) $current,
                'price_per_m3'     => $price,
                'total_amount'     => $total,
            ];
        }

        return $result;
    }
}
