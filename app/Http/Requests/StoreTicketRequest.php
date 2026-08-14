<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Ticket::class);
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:'.Customer::class.',id'],
            'category_id' => ['required', 'exists:'.Category::class.',id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],

            'devices' => ['nullable', 'array'],
            'devices.*.device_type' => ['nullable', 'string', 'max:100'],
            'devices.*.brand' => ['nullable', 'string', 'max:100'],
            'devices.*.model' => ['nullable', 'string', 'max:100'],
            'devices.*.serial_number' => ['nullable', 'string', 'max:100'],
            'devices.*.location' => ['nullable', 'string', 'max:255'],
            'devices.*.installed_at' => ['nullable', 'date'],
            'devices.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Pelanggan wajib dipilih.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'title.required' => 'Judul tiket wajib diisi.',
            'description.required' => 'Deskripsi tiket wajib diisi.',
        ];
    }
}
