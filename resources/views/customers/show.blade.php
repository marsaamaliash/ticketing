<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $customer->name }}</h2>
                <div class="text-sm text-gray-500 font-mono">{{ $customer->customer_code }}</div>
            </div>
            <div class="flex gap-2">
                @can('update', $customer)
                    <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 bg-gray-200 rounded text-sm">Edit</a>
                @endcan
                <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-100 rounded text-sm">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><div class="text-gray-500">Phone</div><div class="font-medium">{{ $customer->phone }}</div></div>
                    <div><div class="text-gray-500">Email</div><div class="font-medium">{{ $customer->email ?? '-' }}</div></div>
                    <div><div class="text-gray-500">Kota</div><div class="font-medium">{{ $customer->city ?? '-' }}</div></div>
                    <div><div class="text-gray-500">Alamat</div><div class="font-medium">{{ $customer->address ?? '-' }}</div></div>
                    @if($customer->notes)
                        <div class="md:col-span-2"><div class="text-gray-500">Catatan</div><div class="font-medium">{{ $customer->notes }}</div></div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded shadow p-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-gray-800">Riwayat Tiket ({{ $customer->tickets()->count() }})</h3>
                    @can('create', \App\Models\Ticket::class)
                        <a href="{{ route('tickets.create', ['customer_id' => $customer->id]) }}" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm">+ Tiket untuk pelanggan ini</a>
                    @endcan
                </div>

                @if($tickets->isEmpty())
                    <p class="text-gray-500 text-sm py-4">Belum ada tiket.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($tickets as $t)
                            <a href="{{ route('tickets.show', $t) }}" class="block py-3 hover:bg-gray-50 -mx-3 px-3 rounded">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-mono text-xs text-gray-500">{{ $t->ticket_number }}</div>
                                        <div class="text-sm font-medium text-gray-800">{{ $t->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $t->category->name }} • {{ $t->created_at->format('d M Y H:i') }} • {{ $t->technician?->name ?? 'belum di-assign' }}</div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <x-status-badge :status="$t->status"/>
                                        <x-priority-badge :priority="$t->priority"/>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-3">{{ $tickets->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
