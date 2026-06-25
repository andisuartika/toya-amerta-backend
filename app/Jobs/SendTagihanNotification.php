<?php

namespace App\Jobs;

use App\Models\WaterReading;
use App\Models\WhatsappTemplate;
use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTagihanNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const DEFAULT_TEMPLATE = "💧 *TAGIHAN AIR PDAM DESA SANGKET*\n\n"
        . "Pelanggan: {nama}\n"
        . "No. Pelanggan: {no_pelanggan}\n"
        . "Periode: {periode}\n\n"
        . "Meter Lalu: {meter_lalu} m³\n"
        . "Meter Ini: {meter_ini} m³\n"
        . "Pemakaian: {pemakaian} m³\n"
        . "Harga/m³: Rp {harga_m3}\n\n"
        . "TOTAL: Rp {total}\n\n"
        . 'Mohon segera melakukan pembayaran. Terima kasih 🙏';

    public function __construct(private int $waterReadingId) {}

    public function handle(FonnteService $fonnte): void
    {
        $reading = WaterReading::with('customer')->find($this->waterReadingId);
        $customer = $reading?->customer;

        if (! $reading || ! $customer || empty($customer->phone)) {
            return;
        }

        $usage = $reading->current_reading - $reading->previous_reading;

        $template = WhatsappTemplate::where('type', 'tagihan')->value('template') ?: self::DEFAULT_TEMPLATE;

        $message = str_replace(
            ['{nama}', '{no_pelanggan}', '{periode}', '{meter_lalu}', '{meter_ini}', '{pemakaian}', '{harga_m3}', '{total}'],
            [
                $customer->name,
                $customer->customer_number,
                $reading->period_label,
                number_format($reading->previous_reading, 1),
                number_format($reading->current_reading, 1),
                number_format($usage, 1),
                number_format($reading->price_per_m3, 0, ',', '.'),
                number_format($reading->total_amount, 0, ',', '.'),
            ],
            $template,
        );

        $log = $fonnte->send(
            $customer->phone,
            $message,
            'tagihan',
            'water_readings',
            $reading->id,
        );

        if ($log->status === 'sent') {
            $reading->forceFill(['wa_sent_at' => now()])->save();
        }
    }
}
