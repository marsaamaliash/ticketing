<?php

namespace App\Observers;

use App\Events\TicketAssigned;
use App\Events\TicketCreated;
use App\Events\TicketFinished;
use App\Models\Ticket;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        TicketCreated::dispatch($ticket);
    }

    public function updated(Ticket $ticket): void
    {
        if ($ticket->wasChanged('assigned_technician_id') && $ticket->assigned_technician_id) {
            TicketAssigned::dispatch($ticket);
        }
        if ($ticket->wasChanged('status') && $ticket->status === 'finished') {
            TicketFinished::dispatch($ticket);
        }
    }
}
