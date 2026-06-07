<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TariffRate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'price_per_m3', 'minimum_charge', 'minimum_usage', 'is_active',
    ];

    protected $casts = [
        'price_per_m3'   => 'decimal:2',
        'minimum_charge' => 'decimal:2',
        'minimum_usage'  => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
