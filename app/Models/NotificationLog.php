<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'notifiable_type', 'notifiable_id', 'recipient_phone',
        'type', 'message', 'status', 'sent_at', 'error_message',
        'provider', 'provider_response',
    ];

    protected $casts = [
        'sent_at'           => 'datetime',
        'provider_response' => 'array',
    ];
}
