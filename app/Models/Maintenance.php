<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    protected $fillable = [
        'title', 'location', 'category', 'zone_id', 'customer_id',
        'reported_by', 'priority', 'status', 'description',
        'photo_before_url', 'photo_after_url',
        'reported_date', 'handled_date', 'completed_date',
        'material_cost', 'labor_cost', 'cash_transaction_id',
    ];

    protected $casts = [
        'reported_date'   => 'date',
        'handled_date'    => 'date',
        'completed_date'  => 'date',
        'material_cost'   => 'float',
        'labor_cost'      => 'float',
        'total_cost'      => 'float',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public static function categoryLabel(string $cat): string
    {
        return match ($cat) {
            'pipa_bocor'      => 'Pipa Bocor',
            'meteran_rusak'   => 'Meteran Rusak',
            'pompa'           => 'Pompa',
            'reservoir'       => 'Reservoir',
            'instalasi_baru'  => 'Instalasi Baru',
            default           => 'Lainnya',
        };
    }

    public static function priorityLabel(string $p): string
    {
        return match ($p) {
            'rendah'  => 'Rendah',
            'sedang'  => 'Sedang',
            'tinggi'  => 'Tinggi',
            'darurat' => 'Darurat',
            default   => $p,
        };
    }

    public static function statusLabel(string $s): string
    {
        return match ($s) {
            'dilaporkan'   => 'Dilaporkan',
            'dalam_proses' => 'Dalam Proses',
            'selesai'      => 'Selesai',
            'ditunda'      => 'Ditunda',
            default        => $s,
        };
    }
}
