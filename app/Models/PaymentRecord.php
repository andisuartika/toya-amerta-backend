<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRecord extends Model
{
    protected $fillable = [
        'water_reading_id', 'customer_id', 'officer_id',
        'amount_paid', 'payment_date', 'payment_method',
        'status', 'receipt_number', 'notes', 'wa_sent_at', 'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'wa_sent_at'   => 'datetime',
        'amount_paid'  => 'float',
    ];

    public function waterReading(): BelongsTo
    {
        return $this->belongsTo(WaterReading::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
