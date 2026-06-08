<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdamBill extends Model
{
    protected $fillable = [
        'period_year', 'period_month',
        'total_m3_from_pdam', 'pdam_price_per_m3',
        'additional_charge', 'total_bill_amount',
        'bill_date', 'due_date',
        'payment_status', 'payment_date',
        'invoice_number', 'notes', 'tier_details',
        'cash_transaction_id', 'created_by',
    ];

    protected $casts = [
        'bill_date'     => 'date',
        'due_date'      => 'date',
        'payment_date'  => 'date',
        'total_m3_from_pdam'  => 'float',
        'pdam_price_per_m3'   => 'float',
        'base_amount'         => 'float',
        'additional_charge'   => 'float',
        'total_bill_amount'   => 'float',
        'tier_details'        => 'array',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cashTransaction(): BelongsTo
    {
        return $this->belongsTo(CashTransaction::class);
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];
        return ($months[$this->period_month] ?? $this->period_month) . ' ' . $this->period_year;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->payment_status === 'belum_bayar'
            && $this->due_date
            && $this->due_date->isPast();
    }
}
