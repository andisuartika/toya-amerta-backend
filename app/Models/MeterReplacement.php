<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeterReplacement extends Model
{
    protected $fillable = [
        'customer_id', 'old_reading_final', 'new_reading_start',
        'replaced_at', 'reason', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'old_reading_final' => 'float',
        'new_reading_start' => 'float',
        'replaced_at'       => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
