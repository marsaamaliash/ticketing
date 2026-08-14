<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        $query = Customer::query()->latest();

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create(): View
    {
        $this->authorize('create', Customer::class);
        $customer = new Customer;

        return view('customers.create', compact('customer'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->authorize('create', Customer::class);
        $customer = Customer::create($request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', "Pelanggan {$customer->customer_code} berhasil dibuat.");
    }

    public function show(Customer $customer): View
    {
        $this->authorize('view', $customer);
        $tickets = $customer->tickets()
            ->with(['category', 'technician', 'creator'])
            ->latest()
            ->paginate(10);

        return view('customers.show', compact('customer', 'tickets'));
    }

    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->authorize('update', $customer);
        $customer->update($request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Data pelanggan diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        if ($customer->tickets()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus pelanggan yang memiliki tiket.');
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan dihapus.');
    }
}
