<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketPdfController extends Controller
{
    public function surat(Ticket $ticket): Response
    {
        $this->authorize('view', $ticket);

        $ticket->load(['customer', 'category', 'creator', 'technician', 'verifier', 'devices', 'attachments']);

        $pdf = Pdf::loadView('pdfs.ticket-surat', [
            'ticket' => $ticket,
            'issuedAt' => now(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $filename = "Surat-{$ticket->ticket_number}.pdf";

        return $pdf->stream($filename);
    }

    public function labelBatch(Request $request)
    {
        $data = $request->validate([
            'ticket_ids' => ['required', 'array', 'min:1'],
            'ticket_ids.*' => ['exists:tickets,id'],
        ]);

        $tickets = Ticket::with(['customer', 'category', 'technician'])
            ->whereIn('id', $data['ticket_ids'])
            ->get();

        if ($tickets->isEmpty()) {
            return back()->with('error', 'Pilih minimal satu tiket.');
        }

        foreach ($tickets as $t) {
            $this->authorize('view', $t);
        }

        $pdf = Pdf::loadView('pdfs.device-labels', [
            'tickets' => $tickets,
            'issuedAt' => now(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $filename = 'Label-Perangkat-'.now()->format('Ymd-His').'.pdf';

        return $pdf->stream($filename);
    }
}
