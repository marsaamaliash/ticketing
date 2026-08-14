<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiagnosisRequest;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketDiagnosis;
use Illuminate\Http\RedirectResponse;

class TicketDiagnosisController extends Controller
{
    public function store(StoreDiagnosisRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('inputDiagnosis', $ticket);

        $data = $request->validated();
        $data['ticket_id'] = $ticket->id;
        $data['technician_id'] = $request->user()->id;
        $data['status'] = 'pending';

        $diagnosis = TicketDiagnosis::updateOrCreate(
            [
                'ticket_id' => $ticket->id,
                'technician_id' => $request->user()->id,
            ],
            $data,
        );

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'type' => 'diagnosis',
            'description' => 'Teknisi menambahkan diagnosis: '.substr($data['diagnosis_text'], 0, 120),
        ]);

        return back()->with('success', 'Diagnosis disimpan.');
    }
}
