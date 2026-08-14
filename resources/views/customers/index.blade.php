<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Pelanggan') }}</h2>
            @can('create', \App\Models\Customer::class)
                <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">+ Pelanggan Baru</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('customers.index') }}" class="bg-white p-4 rounded shadow mb-4 flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / phone / kode / alamat" class="flex-1 border-gray-300 rounded-md text-sm">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Cari</button>
                @if(request('q'))
                    <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-200 rounded text-sm">Reset</a>
                @endif
            </form>

            <div class="bg-white rounded shadow overflow-hidden">
                @if($customers->isEmpty())
                    <div class="p-6 text-center text-gray-500">Belum ada data pelanggan.</div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customers as $c)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-sm text-gray-600">{{ $c->customer_code }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $c->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $c->phone }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ Str::limit($c->address, 40) }}</td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <a href="{{ route('customers.show', $c) }}" class="text-blue-600 hover:underline">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-3">{{ $customers->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
