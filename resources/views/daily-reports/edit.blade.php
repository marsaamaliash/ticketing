<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Daily Report</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6">
                <form method="POST" action="{{ route('daily-reports.update', $dailyReport) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal *</label>
                        <input type="date" name="report_date" value="{{ old('report_date', $dailyReport->report_date->format('Y-m-d')) }}" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Aktivitas *</label>
                        <input type="text" name="activity" value="{{ old('activity', $dailyReport->activity) }}" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tiket</label>
                        <select name="ticket_id" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                            <option value="">— Tidak ada —</option>
                            @foreach($myTickets as $t)
                                <option value="{{ $t->id }}" @selected($dailyReport->ticket_id == $t->id)>{{ $t->ticket_number }} — {{ Str::limit($t->title, 50) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700">Jam Mulai</label><input type="time" name="start_time" value="{{ old('start_time', $dailyReport->start_time?->format('H:i')) }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700">Jam Selesai</label><input type="time" name="end_time" value="{{ old('end_time', $dailyReport->end_time?->format('H:i')) }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700">Lokasi</label><input type="text" name="location" value="{{ old('location', $dailyReport->location) }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Catatan Progress *</label><textarea name="progress_note" rows="4" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('progress_note', $dailyReport->progress_note) }}</textarea></div>
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('daily-reports.show', $dailyReport) }}" class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</a>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
