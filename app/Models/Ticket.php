<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'category_id',
        'created_by',
        'assigned_technician_id',
        'title',
        'description',
        'status',
        'priority',
        'scheduled_at',
        'started_at',
        'finished_at',
        'verified_at',
        'verified_by',
        'rating',
        'rating_comment',
        'cancellation_reason',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'verified_at' => 'datetime',
        'rating' => 'integer',
    ];

    public const STATUSES = [
        'open' => 'Open',
        'forwarded' => 'Forwarded',
        'assigned' => 'Assigned',
        'in_progress' => 'In Progress',
        'finished' => 'Finished',
        'verified' => 'Verified',
        'closed' => 'Closed',
        'reopened' => 'Reopened',
        'cancelled' => 'Cancelled',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = static::generateNumber();
            }
        });
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $prefix = "TKT-{$year}-";
        $latest = static::where('ticket_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('ticket_number');

        $nextSeq = 1;
        if ($latest && preg_match('/(\d+)$/', $latest, $m)) {
            $nextSeq = ((int) $m[1]) + 1;
        }

        return $prefix.str_pad((string) $nextSeq, 5, '0', STR_PAD_LEFT);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(TicketDevice::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class)->latest();
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(TicketDiagnosis::class);
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class);
    }

    public function scopeForUser($query, User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            return $query;
        }
        if ($user->hasRole('cs')) {
            return $query;
        }
        if ($user->hasRole('teknisi')) {
            return $query->where('assigned_technician_id', $user->id);
        }

        return $query->whereRaw('0 = 1');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'gray',
            'forwarded' => 'blue',
            'assigned' => 'indigo',
            'in_progress' => 'yellow',
            'finished' => 'green',
            'verified' => 'emerald',
            'closed' => 'zinc',
            'reopened' => 'orange',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray',
        };
    }
}
