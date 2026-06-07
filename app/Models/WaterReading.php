<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterReading extends Model
{
    protected $fillable = [
        'customer_id', 'officer_id', 'period_year', 'period_month',
        'previous_reading', 'current_reading',
        'tariff_rate_id', 'price_per_m3', 'minimum_charge', 'minimum_usage',
        'total_amount', 'reading_date', 'photo_url', 'notes',
        'payment_status', 'wa_sent_at', 'updated_by',
    ];

    protected $casts = [
        'reading_date'     => 'date',
        'wa_sent_at'       => 'datetime',
        'previous_reading' => 'float',
        'current_reading'  => 'float',
        'usage_m3'         => 'float',
        'total_amount'     => 'float',
        'price_per_m3'     => 'float',
        'minimum_charge'   => 'float',
        'minimum_usage'    => 'float',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function tariffRate(): BelongsTo
    {
        return $this->belongsTo(TariffRate::class);
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return ($months[$this->period_month] ?? $this->period_month) . ' ' . $this->period_year;
    }
}
