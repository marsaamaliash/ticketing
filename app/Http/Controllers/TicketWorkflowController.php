<?php

namespace App\Http\Controllers;

use App\Events\TicketReopened;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketWorkflowController extends Controller
{
    public function forward(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('forward', $ticket);

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $ticket, $data) {
            $ticket->update(['status' => 'forwarded']);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'forwarded',
                'description' => 'Tiket diteruskan ke manager untuk assignment teknisi.'.($data['note'] ?? null ? " Catatan: {$data['note']}" : ''),
                'meta' => $data,
            ]);
        });

        return back()->with('success', 'Tiket diteruskan ke manager.');
    }

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('assign', $ticket);

        $data = $request->validate([
            'assigned_technician_id' => ['required', 'exists:users,id'],
            'scheduled_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $scheduledAt = $request->input('scheduled_at');
        $note = $request->input('note');

        DB::transaction(function () use ($request, $ticket, $data, $scheduledAt, $note) {
            $technician = User::findOrFail($data['assigned_technician_id']);
            $ticket->update([
                'assigned_technician_id' => $technician->id,
                'status' => 'assigned',
                'scheduled_at' => $scheduledAt,
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'assigned',
                'description' => "Tiket di-assign ke teknisi {$technician->name}.".($scheduledAt ? " Jadwal: {$scheduledAt}." : ''),
                'meta' => [
                    'technician_id' => $technician->id,
                    'technician_name' => $technician->name,
                    'scheduled_at' => $scheduledAt,
                    'note' => $note,
                ],
            ]);
        });

        return back()->with('success', 'Teknisi berhasil di-assign.');
    }

    public function reschedule(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('schedule', $ticket);

        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
        ]);

        $oldScheduled = $ticket->scheduled_at;
        $ticket->update(['scheduled_at' => $data['scheduled_at']]);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'type' => 'scheduled',
            'description' => 'Jadwal diubah dari '.($oldScheduled ? $oldScheduled->format('d-m-Y H:i') : '-')." menjadi {$data['scheduled_at']}.",
            'meta' => ['old' => $oldScheduled?->toIso8601String(), 'new' => $data['scheduled_at']],
        ]);

        return back()->with('success', 'Jadwal diperbarui.');
    }

    public function startProgress(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('startProgress', $ticket);

        DB::transaction(function () use ($request, $ticket) {
            $ticket->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'status_change',
                'description' => 'Pekerjaan dimulai oleh teknisi.',
            ]);
        });

        return back()->with('success', 'Status tiket diubah ke In Progress.');
    }

    public function markFinished(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('markFinished', $ticket);

        $data = $request->validate([
            'action_taken' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $ticket, $data) {
            $ticket->update([
                'status' => 'finished',
                'finished_at' => now(),
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'status_change',
                'description' => "Tiket ditandai selesai oleh {$request->user()->name}. Tindakan: {$data['action_taken']}",
            ]);
        });

        return back()->with('success', 'Tiket ditandai selesai. Menunggu verifikasi CS.');
    }

    public function verify(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('verify', $ticket);

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $ticket, $data) {
            $ticket->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => $request->user()->id,
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'verified',
                'description' => 'Tiket diverifikasi oleh CS.'.($data['note'] ?? null ? " Catatan: {$data['note']}" : ''),
            ]);
        });

        return back()->with('success', 'Tiket diverifikasi. Silakan berikan rating.');
    }

    public function rate(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('rate', $ticket);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'rating_comment' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $ticket, $data) {
            $ticket->update([
                'rating' => $data['rating'],
                'rating_comment' => $data['rating_comment'] ?? null,
                'status' => 'closed',
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'rated',
                'description' => "Tiket diberi rating {$data['rating']}/5 oleh {$request->user()->name}.",
                'meta' => ['rating' => $data['rating'], 'comment' => $data['rating_comment'] ?? null],
            ]);
        });

        return back()->with('success', 'Tiket ditutup.');
    }

    public function reopen(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('reopen', $ticket);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $reason = $data['reason'];

        DB::transaction(function () use ($request, $ticket, $reason) {
            $newStatus = $ticket->assigned_technician_id ? 'in_progress' : 'reopened';

            $ticket->update([
                'status' => $newStatus,
                'finished_at' => null,
                'verified_at' => null,
                'verified_by' => null,
                'rating' => null,
                'rating_comment' => null,
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'reopened',
                'description' => "Tiket dibuka kembali. Alasan: {$reason}",
            ]);
        });

        event(new TicketReopened($ticket, $reason));

        return back()->with('success', 'Tiket dibuka kembali untuk perbaikan.');
    }

    public function cancel(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('cancel', $ticket);

        $data = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $ticket, $data) {
            $ticket->update([
                'status' => 'cancelled',
                'cancellation_reason' => $data['cancellation_reason'],
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'cancelled',
                'description' => "Tiket dibatalkan. Alasan: {$data['cancellation_reason']}",
            ]);
        });

        return back()->with('success', 'Tiket dibatalkan.');
    }

    public function comment(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('view', $ticket);

        $data = $request->validate([
            'description' => ['required', 'string', 'max:2000'],
        ]);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'type' => 'comment',
            'description' => $data['description'],
        ]);

        return back()->with('success', 'Komentar ditambahkan.');
    }
}
