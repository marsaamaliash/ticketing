<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketFinishedNotification extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => 'Tiket selesai, perlu verifikasi',
            'body' => "{$this->ticket->technician?->name} menandai tiket {$this->ticket->ticket_number} selesai.",
            'icon' => '✅',
            'url' => route('tickets.show', $this->ticket),
        ];
    }
}
