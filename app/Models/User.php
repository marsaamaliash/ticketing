<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function createdTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_technician_id');
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'technician_id');
    }

    public function ticketActivities(): HasMany
    {
        return $this->hasMany(TicketActivity::class);
    }

    public function ticketAttachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'uploaded_by');
    }

    public function ticketDiagnoses(): HasMany
    {
        return $this->hasMany(TicketDiagnosis::class, 'technician_id');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isCs(): bool
    {
        return $this->hasRole('cs');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isTeknisi(): bool
    {
        return $this->hasRole('teknisi');
    }

    public function getRoleListAttribute(): string
    {
        return $this->roles->pluck('name')->implode(', ');
    }
}
