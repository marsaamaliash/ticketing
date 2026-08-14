<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'technician_id',
        'ticket_id',
        'report_date',
        'activity',
        'progress_note',
        'location',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'report_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
