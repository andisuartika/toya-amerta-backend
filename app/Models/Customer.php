<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'customer_number', 'name', 'address',
        'phone', 'zone_id', 'tariff_rate_id',
        'installation_date', 'initial_meter', 'is_active', 'notes',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'initial_meter'     => 'decimal:2',
        'is_active'         => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function tariffRate(): BelongsTo
    {
        return $this->belongsTo(TariffRate::class);
    }
}
