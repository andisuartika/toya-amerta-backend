<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['name', 'code', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
