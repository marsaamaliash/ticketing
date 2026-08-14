<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('inputDiagnosis', $this->route('ticket'));
    }

    public function rules(): array
    {
        return [
            'diagnosis_text' => ['required', 'string', 'max:2000'],
            'root_cause' => ['nullable', 'string', 'max:1000'],
            'action_taken' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
