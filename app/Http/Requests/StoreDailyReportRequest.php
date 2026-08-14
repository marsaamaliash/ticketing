<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['teknisi', 'admin']);
    }

    public function rules(): array
    {
        return [
            'ticket_id' => ['nullable', 'exists:tickets,id'],
            'report_date' => ['required', 'date'],
            'activity' => ['required', 'string', 'max:255'],
            'progress_note' => ['required', 'string', 'max:3000'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        ];
    }
}
