<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewCustomerFee extends Model
{
    protected $fillable = [
        'customer_id', 'fee_type', 'amount', 'description',
        'payment_date', 'payment_method', 'receipt_number',
        'recorded_by', 'cash_transaction_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'float',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function feeTypeLabel(string $type): string
    {
        return match ($type) {
            'biaya_pendaftaran' => 'Biaya Pendaftaran',
            'biaya_instalasi'   => 'Biaya Instalasi',
            'biaya_meteran'     => 'Biaya Meteran',
            'uang_jaminan'      => 'Uang Jaminan',
            default             => 'Lainnya',
        };
    }
}
