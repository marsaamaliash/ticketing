<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketReopenedNotification extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket, public string $reason = '') {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => 'Tiket dibuka kembali',
            'body' => 'Tiket '.$this->ticket->ticket_number.' dibuka kembali. '.mb_substr($this->reason, 0, 100),
            'icon' => '🔁',
            'url' => route('tickets.show', $this->ticket),
        ];
    }
}
