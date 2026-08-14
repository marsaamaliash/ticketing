<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'device_type',
        'brand',
        'model',
        'serial_number',
        'location',
        'installed_at',
        'notes',
    ];

    protected $casts = [
        'installed_at' => 'date',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
