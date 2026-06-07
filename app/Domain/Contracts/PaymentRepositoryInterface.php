<?php

namespace App\Domain\Contracts;

use App\Domain\DTOs\Payment\PaymentDTO;
use App\Models\PaymentRecord;
use Illuminate\Support\Collection;

interface PaymentRepositoryInterface
{
    public function create(PaymentDTO $dto, int $recordedBy): PaymentRecord;

    public function findById(int $id): PaymentRecord;

    public function delete(int $id): void;

    /** Tagihan belum lunas — untuk halaman konfirmasi */
    public function unpaid(array $filters = []): Collection;

    /** Riwayat pembayaran */
    public function history(array $filters = []): Collection;

    /** Generate nomor kwitansi */
    public function generateReceiptNumber(): string;
}
