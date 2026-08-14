<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $query = Ticket::with(['customer', 'category', 'technician', 'creator'])
            ->forUser($request->user())
            ->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($priority = $request->string('priority')->toString()) {
            $query->where('priority', $priority);
        }
        if ($categoryId = $request->integer('category_id')) {
            $query->where('category_id', $categoryId);
        }
        if ($technicianId = $request->integer('technician_id')) {
            $query->where('assigned_technician_id', $technicianId);
        }
        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }
        if ($from = $request->date('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->date('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $tickets = $query->paginate(20)->withQueryString();

        $categories = Category::orderBy('name')->get();

        $technicians = User::role('teknisi')->orderBy('name')->get();

        return view('tickets.index', compact(
            'tickets', 'categories', 'technicians',
        ));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Ticket::class);
        $categories = Category::orderBy('name')->get();
        $customer = null;

        if ($customerId = $request->integer('customer_id')) {
            $customer = Customer::find($customerId);
        }

        return view('tickets.create', compact('categories', 'customer'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $this->authorize('create', Ticket::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['status'] = 'open';

        $customer = Customer::findOrFail($data['customer_id']);

        $ticket = Ticket::create($data);

        if (! empty($data['devices'])) {
            foreach ($data['devices'] as $deviceData) {
                if (! empty($deviceData['brand']) || ! empty($deviceData['serial_number'])) {
                    $ticket->devices()->create($deviceData);
                }
            }
        }

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'type' => 'created',
            'description' => "Tiket {$ticket->ticket_number} dibuat oleh {$request->user()->name} untuk pelanggan {$customer->name} ({$customer->customer_code}).",
        ]);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Tiket {$ticket->ticket_number} berhasil dibuat.");
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'customer',
            'category',
            'creator',
            'technician',
            'verifier',
            'devices',
            'attachments.uploader',
            'activities.user',
            'diagnoses.technician',
        ]);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);
        $categories = Category::orderBy('name')->get();

        return view('tickets.edit', compact('ticket', 'categories'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $old = $ticket->getOriginal();
        $ticket->update($request->validated());

        if ($old['priority'] !== $ticket->priority) {
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'status_change',
                'description' => "Priority diubah dari {$old['priority']} menjadi {$ticket->priority}.",
                'meta' => ['old' => $old['priority'], 'new' => $ticket->priority],
            ]);
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Tiket diperbarui.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);
        $number = $ticket->ticket_number;
        $ticket->delete();

        return redirect()
            ->route('tickets.index')
            ->with('success', "Tiket {$number} dihapus.");
    }
}
