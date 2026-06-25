<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Kirim pesan WA via Fonnte dan catat hasilnya ke notification_logs.
     */
    public function send(string $phone, string $message, string $type, string $notifiableType, int $notifiableId): NotificationLog
    {
        $target = $this->normalizePhone($phone);

        $log = NotificationLog::create([
            'notifiable_type'  => $notifiableType,
            'notifiable_id'    => $notifiableId,
            'recipient_phone'  => $target,
            'type'             => $type,
            'message'          => $message,
            'status'           => 'pending',
            'provider'         => 'fonnte',
        ]);

        if (! config('services.fonnte.enabled') || empty(config('services.fonnte.token'))) {
            $log->update([
                'status'         => 'failed',
                'error_message'  => 'Fonnte token belum dikonfigurasi atau notifikasi dimatikan.',
            ]);

            return $log;
        }

        try {
            $response = Http::asForm()
                ->withHeaders(['Authorization' => config('services.fonnte.token')])
                ->post(config('services.fonnte.url'), [
                    'target'  => $target,
                    'message' => $message,
                ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                $log->update([
                    'status'             => 'sent',
                    'sent_at'            => now(),
                    'provider_response'  => $body,
                ]);
            } else {
                $log->update([
                    'status'             => 'failed',
                    'error_message'      => $body['reason'] ?? 'Gagal mengirim pesan WA.',
                    'provider_response'  => $body,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Fonnte send failed', ['error' => $e->getMessage(), 'phone' => $target]);

            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $log;
    }

    /**
     * Fonnte mengharuskan format nomor diawali 62 (tanpa +), bukan 0.
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (! str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }
}
