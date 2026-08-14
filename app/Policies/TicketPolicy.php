<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view-tickets')
            || $user->can('view-all-tickets')
            || $user->hasRole('cs|manager|teknisi');
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole('manager')) {
            return $user->can('view-all-tickets');
        }
        if ($user->hasRole('cs')) {
            return $user->can('view-tickets');
        }
        if ($user->hasRole('teknisi')) {
            return $ticket->assigned_technician_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create-tickets');
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole('cs')) {
            return $user->can('edit-tickets')
                && $ticket->created_by === $user->id
                && in_array($ticket->status, ['open', 'reopened'], true);
        }

        return $user->can('edit-tickets');
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('admin')
            && in_array($ticket->status, ['open', 'cancelled'], true);
    }

    public function forward(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('cs')
            && $user->can('forward-to-technician')
            && $ticket->status === 'open';
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('manager')
            && $user->can('assign-technicians')
            && in_array($ticket->status, ['forwarded', 'reopened'], true);
    }

    public function schedule(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('manager')
            && $user->can('schedule-tickets');
    }

    public function inputDiagnosis(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('teknisi')
            && $user->can('input-diagnosis')
            && $ticket->assigned_technician_id === $user->id
            && in_array($ticket->status, ['assigned', 'in_progress'], true);
    }

    public function startProgress(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('teknisi')
            && $ticket->assigned_technician_id === $user->id
            && $ticket->status === 'assigned';
    }

    public function markFinished(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('teknisi')
            && $user->can('mark-ticket-finished')
            && $ticket->assigned_technician_id === $user->id
            && in_array($ticket->status, ['assigned', 'in_progress'], true);
    }

    public function verify(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('cs')
            && $user->can('verify-completion')
            && $ticket->status === 'finished';
    }

    public function reopen(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('cs')
            && $user->can('reopen-tickets')
            && in_array($ticket->status, ['finished', 'verified', 'closed'], true);
    }

    public function rate(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('cs')
            && $user->can('rate-tickets')
            && $ticket->status === 'verified';
    }

    public function cancel(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('cs')
            && $ticket->created_by === $user->id
            && in_array($ticket->status, ['open', 'reopened', 'forwarded'], true);
    }
}
