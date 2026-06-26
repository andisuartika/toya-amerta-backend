<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterMeterReading extends Model
{
    protected $fillable = [
        'period_year', 'period_month',
        'previous_reading', 'current_reading',
        'reading_date', 'photo_url', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'reading_date'     => 'date',
        'previous_reading' => 'float',
        'current_reading'  => 'float',
        'usage_m3'         => 'float',
    ];

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
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
