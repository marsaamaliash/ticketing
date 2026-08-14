<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Tiket {{ $ticket->ticket_number }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6">
                <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <x-input-label value="Pelanggan" />
                        <div class="block mt-1 w-full border border-gray-200 bg-gray-50 rounded-md p-2 text-sm">
                            {{ $ticket->customer->customer_code }} — {{ $ticket->customer->name }} ({{ $ticket->customer->phone }})
                        </div>
                    </div>

                    <div>
                        <x-input-label value="Kategori" />
                        <div class="block mt-1 w-full border border-gray-200 bg-gray-50 rounded-md p-2 text-sm">{{ $ticket->category->name }}</div>
                    </div>

                    <div>
                        <x-input-label for="title" value="Judul *" />
                        <input id="title" name="title" type="text" value="{{ old('title', $ticket->title) }}" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                        <x-input-error :messages="$errors->get('title')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi *" />
                        <textarea id="description" name="description" rows="5" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('description', $ticket->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label for="priority" value="Priority *" />
                        <select id="priority" name="priority" required class="block mt-1 w-full border-gray-300 rounded-md text-sm w-48">
                            @foreach(\App\Models\Ticket::PRIORITIES as $k => $v)
                                <option value="{{ $k }}" @selected(old('priority', $ticket->priority) === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('tickets.show', $ticket) }}" class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
