<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttachmentRequest;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function store(StoreAttachmentRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('view', $ticket);

        $files = $request->file('files', []);

        if (! is_array($files)) {
            $files = [$files];
        }

        $uploaded = 0;
        foreach ($files as $file) {
            if (! $file) {
                continue;
            }
            $path = $file->store("tickets/{$ticket->id}", 'public');
            $att = TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'uploaded_by' => $request->user()->id,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
            $uploaded++;

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'attachment',
                'description' => 'Upload lampiran: '.$att->original_name,
            ]);
        }

        return back()->with('success', $uploaded.' lampiran diunggah.');
    }

    public function download(Ticket $ticket, TicketAttachment $attachment)
    {
        $this->authorize('view', $attachment);

        if ($attachment->ticket_id !== $ticket->id) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->original_name,
        );
    }

    public function destroy(Ticket $ticket, TicketAttachment $attachment): RedirectResponse
    {
        $this->authorize('delete', $attachment);

        if ($attachment->ticket_id !== $ticket->id) {
            abort(404);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'type' => 'attachment',
            'description' => 'Menghapus lampiran: '.$attachment->original_name,
        ]);

        return back()->with('success', 'Lampiran dihapus.');
    }
}
