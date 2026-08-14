<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Tiket Baru</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6">
                <form method="POST" action="{{ route('tickets.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="customer_id" value="Pelanggan *" />
                            @if($customer)
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                <div class="block mt-1 w-full border border-gray-200 bg-gray-50 rounded-md p-2 text-sm">
                                    <div class="font-mono text-xs text-gray-500">{{ $customer->customer_code }}</div>
                                    <div class="font-medium">{{ $customer->name }} — {{ $customer->phone }}</div>
                                    <div class="text-xs text-gray-500">{{ $customer->address }}</div>
                                    <a href="{{ route('customers.create') }}?redirect=tickets" class="text-xs text-blue-600 hover:underline">Pelanggan lain</a>
                                </div>
                            @else
                                <div class="flex gap-2 mt-1">
                                    <select id="customer_id" name="customer_id" required class="flex-1 border-gray-300 rounded-md text-sm">
                                        <option value="">— Pilih pelanggan —</option>
                                        @foreach(\App\Models\Customer::orderBy('name')->limit(200)->get() as $c)
                                            <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->customer_code }} — {{ $c->name }} ({{ $c->phone }})</option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('customers.create') }}?redirect=tickets" class="px-3 py-2 bg-green-600 text-white rounded text-sm whitespace-nowrap">+ Baru</a>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Tidak ada? <a href="{{ route('customers.create') }}?redirect=tickets" class="text-blue-600 hover:underline">Daftarkan pelanggan baru</a>.</p>
                            @endif
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-1"/>
                        </div>

                        <div>
                            <x-input-label for="category_id" value="Kategori *" />
                            <select id="category_id" name="category_id" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                                <option value="">— Pilih kategori —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-1"/>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="title" value="Judul Tiket *" />
                        <input id="title" name="title" type="text" value="{{ old('title') }}" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                        <x-input-error :messages="$errors->get('title')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi / Detail Keluhan *" />
                        <textarea id="description" name="description" rows="4" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label for="priority" value="Priority *" />
                        <select id="priority" name="priority" required class="block mt-1 w-full border-gray-300 rounded-md text-sm w-48">
                            @foreach(\App\Models\Ticket::PRIORITIES as $k => $v)
                                <option value="{{ $k }}" @selected(old('priority', 'medium') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-data="{ deviceCount: 1 }">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-sm font-semibold text-gray-700">Perangkat Terkait (opsional)</h3>
                            <button type="button" @click="deviceCount++" class="text-sm text-blue-600 hover:underline">+ Tambah Perangkat</button>
                        </div>
                        <template x-for="i in deviceCount" :key="i">
                            <div class="border border-gray-200 rounded p-3 mb-3 grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                                <div>
                                    <label class="text-xs text-gray-500">Tipe</label>
                                    <input :name="`devices[${i-1}][device_type]`" type="text" placeholder="Modem/ONT/Router" class="mt-1 w-full border-gray-300 rounded-md text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Brand</label>
                                    <input :name="`devices[${i-1}][brand]`" type="text" placeholder="ZTE/Huawei" class="mt-1 w-full border-gray-300 rounded-md text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Model</label>
                                    <input :name="`devices[${i-1}][model]`" type="text" placeholder="F609" class="mt-1 w-full border-gray-300 rounded-md text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Serial Number</label>
                                    <input :name="`devices[${i-1}][serial_number]`" type="text" placeholder="SNxxxxx" class="mt-1 w-full border-gray-300 rounded-md text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Lokasi</label>
                                    <input :name="`devices[${i-1}][location]`" type="text" placeholder="Ruang tamu" class="mt-1 w-full border-gray-300 rounded-md text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Tgl Instalasi</label>
                                    <input :name="`devices[${i-1}][installed_at]`" type="date" class="mt-1 w-full border-gray-300 rounded-md text-sm">
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('tickets.index') }}" class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Simpan Tiket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
