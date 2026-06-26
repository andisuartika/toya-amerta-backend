<?php

namespace App\Jobs;

use App\Models\PaymentRecord;
use App\Models\WhatsappTemplate;
use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendKonfirmasiBayarNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const DEFAULT_TEMPLATE = "✅ *KONFIRMASI PEMBAYARAN PDAM DESA SANGKET*\n\n"
        . "Pelanggan: {nama}\n"
        . "No. Kwitansi: {no_kwitansi}\n"
        . "Periode: {periode}\n\n"
        . "Jumlah Bayar: Rp {jumlah_bayar}\n"
        . "Status Tagihan: {status}\n"
        . "{sisa_block}\n"
        . "Cek detail: {link}\n\n"
        . 'Terima kasih atas pembayaran Anda 🙏';

    public function __construct(private int $paymentRecordId) {}

    public function handle(FonnteService $fonnte): void
    {
        $payment = PaymentRecord::with(['customer', 'waterReading'])->find($this->paymentRecordId);
        $customer = $payment?->customer;

        if (! $payment || ! $customer || empty($customer->phone)) {
            return;
        }

        $reading = $payment->waterReading;
        $isLunas = $reading?->payment_status === 'lunas';

        $sisaBlock = '';
        if (! $isLunas && $reading) {
            $totalPaid = PaymentRecord::where('water_reading_id', $reading->id)->sum('amount_paid');
            $remaining = max(0, $reading->total_amount - $totalPaid);

            $sisaBlock = 'Sisa Tagihan: Rp ' . number_format($remaining, 0, ',', '.');
        }

        $link = $reading
            ? route('public.cek.detail', [$customer->customer_number, $reading->id])
            : route('public.cek.history', $customer->customer_number);

        $template = WhatsappTemplate::where('type', 'konfirmasi_bayar')->value('template') ?: self::DEFAULT_TEMPLATE;

        $message = str_replace(
            ['{nama}', '{no_kwitansi}', '{periode}', '{jumlah_bayar}', '{status}', '{sisa_block}', '{link}'],
            [
                $customer->name,
                $payment->receipt_number,
                $reading?->period_label,
                number_format($payment->amount_paid, 0, ',', '.'),
                $isLunas ? 'LUNAS' : 'Sebagian',
                $sisaBlock,
                $link,
            ],
            $template,
        );

        $log = $fonnte->send(
            $customer->phone,
            $message,
            'konfirmasi_bayar',
            'payment_records',
            $payment->id,
        );

        if ($log->status === 'sent') {
            $payment->forceFill(['wa_sent_at' => now()])->save();
        }
    }
}
