<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'type',
        'description',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public const TYPES = [
        'created' => 'Ticket Created',
        'status_change' => 'Status Changed',
        'assigned' => 'Technician Assigned',
        'scheduled' => 'Scheduled',
        'forwarded' => 'Forwarded',
        'comment' => 'Comment',
        'diagnosis' => 'Diagnosis',
        'daily_report' => 'Daily Report',
        'attachment' => 'Attachment',
        'verified' => 'Verified',
        'rated' => 'Rated',
        'reopened' => 'Reopened',
        'cancelled' => 'Cancelled',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'created' => 'blue',
            'status_change' => 'indigo',
            'assigned' => 'purple',
            'scheduled' => 'cyan',
            'forwarded' => 'sky',
            'comment' => 'gray',
            'diagnosis' => 'yellow',
            'daily_report' => 'lime',
            'attachment' => 'slate',
            'verified' => 'green',
            'rated' => 'emerald',
            'reopened' => 'orange',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }
}
