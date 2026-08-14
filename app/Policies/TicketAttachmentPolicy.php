<?php

namespace App\Policies;

use App\Models\TicketAttachment;
use App\Models\User;

class TicketAttachmentPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function view(User $user, TicketAttachment $attachment): bool
    {
        $ticket = $attachment->ticket;

        if ($user->hasRole('manager')) {
            return true;
        }
        if ($user->hasRole('cs')) {
            return $ticket->created_by === $user->id || $user->can('view-tickets');
        }
        if ($user->hasRole('teknisi')) {
            return $ticket->assigned_technician_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, TicketAttachment $attachment): bool
    {
        return $attachment->uploaded_by === $user->id || $user->hasRole('admin');
    }
}
