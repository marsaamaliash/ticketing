<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Daftar Tiket') }}</h2>
            <div class="flex gap-2">
                @can('create', \App\Models\Ticket::class)
                    <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded text-sm">+ Buat Tiket</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('tickets.index') }}" class="bg-white p-4 rounded shadow mb-4 grid grid-cols-2 md:grid-cols-5 gap-2 text-sm">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor / judul / pelanggan" class="col-span-2 md:col-span-2 border-gray-300 rounded-md">
                <select name="status" class="border-gray-300 rounded-md">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\Ticket::STATUSES as $k => $v)
                        <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                <select name="priority" class="border-gray-300 rounded-md">
                    <option value="">Semua Priority</option>
                    @foreach(\App\Models\Ticket::PRIORITIES as $k => $v)
                        <option value="{{ $k }}" @selected(request('priority') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                <select name="category_id" class="border-gray-300 rounded-md">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="technician_id" class="border-gray-300 rounded-md">
                    <option value="">Semua Teknisi</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" @selected(request('technician_id') == $tech->id)>{{ $tech->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="border-gray-300 rounded-md">
                <input type="date" name="to" value="{{ request('to') }}" class="border-gray-300 rounded-md">
                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded">Filter</button>
                <a href="{{ route('tickets.index') }}" class="px-3 py-1.5 bg-gray-200 rounded text-center">Reset</a>
            </form>

            <form action="{{ route('tickets.labels.preview') }}" method="POST" id="labelForm">
                @csrf
                <div class="bg-white rounded shadow overflow-hidden">
                    @if($tickets->isEmpty())
                        <div class="p-6 text-center text-gray-500">Tidak ada tiket.</div>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 w-8"><input type="checkbox" id="checkAll"/></th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Tiket</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teknisi</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl</th>
                                    <th class="px-3 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($tickets as $t)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2"><input type="checkbox" name="ticket_ids[]" value="{{ $t->id }}" class="ticket-check"/></td>
                                        <td class="px-3 py-2 font-mono text-xs text-gray-700">{{ $t->ticket_number }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-800">{{ Str::limit($t->title, 40) }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-700">{{ $t->customer->name }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600">{{ $t->category->name }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600">{{ $t->technician?->name ?? '-' }}</td>
                                        <td class="px-3 py-2"><x-status-badge :status="$t->status"/></td>
                                        <td class="px-3 py-2"><x-priority-badge :priority="$t->priority"/></td>
                                        <td class="px-3 py-2 text-xs text-gray-500">{{ $t->created_at->format('d M Y') }}</td>
                                        <td class="px-3 py-2 text-right">
                                            <a href="{{ route('tickets.show', $t) }}" class="text-blue-600 hover:underline text-sm">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="mt-3 flex justify-between items-center">
                    <button type="submit" class="px-3 py-1.5 bg-purple-600 text-white rounded text-sm">🖨 Cetak Label Device (yang dipilih)</button>
                    <div>{{ $tickets->links() }}</div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('checkAll')?.addEventListener('change', function(e){
            document.querySelectorAll('.ticket-check').forEach(c => c.checked = e.target.checked);
        });
    </script>
</x-app-layout>
