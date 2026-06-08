<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CashTransaction extends Model
{
    protected $fillable = [
        'transaction_date', 'type', 'category', 'amount',
        'description', 'reference_type', 'reference_id',
        'attachment_url', 'recorded_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'float',
    ];

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function categoryOptions(): array
    {
        return [
            'masuk'  => [
                'Pembayaran Tagihan Air',
                'Biaya Pemasangan Pelanggan Baru',
                'Uang Jaminan',
                'Pendapatan Lainnya',
            ],
            'keluar' => [
                'Biaya Maintenance',
                'Tagihan PDAM Pusat',
                'Biaya Operasional',
                'Biaya Tenaga Kerja',
                'Pembelian Material',
                'Biaya Administrasi',
                'Pengeluaran Lainnya',
            ],
        ];
    }
}
