<?php

namespace App\Providers;

use App\Events\TicketAssigned;
use App\Events\TicketCreated;
use App\Events\TicketFinished;
use App\Events\TicketReopened;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketFinishedNotification;
use App\Notifications\TicketReopenedNotification;
use App\Observers\TicketObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Ticket::observe(TicketObserver::class);

        Event::listen(
            TicketCreated::class,
            function (TicketCreated $event) {
                $managers = User::role('manager')->get();
                if ($managers->isNotEmpty()) {
                    Notification::send($managers, new TicketCreatedNotification($event->ticket));
                }
            }
        );

        Event::listen(
            TicketAssigned::class,
            function (TicketAssigned $event) {
                if ($event->ticket->technician) {
                    $event->ticket->technician->notify(new TicketAssignedNotification($event->ticket));
                }
            }
        );

        Event::listen(
            TicketFinished::class,
            function (TicketFinished $event) {
                $cs = User::role('cs')->get();
                if ($cs->isNotEmpty()) {
                    Notification::send($cs, new TicketFinishedNotification($event->ticket));
                }
            }
        );

        Event::listen(
            TicketReopened::class,
            function (TicketReopened $event) {
                if ($event->ticket->technician) {
                    $event->ticket->technician->notify(new TicketReopenedNotification($event->ticket, $event->reason));
                }
            }
        );
    }
}
