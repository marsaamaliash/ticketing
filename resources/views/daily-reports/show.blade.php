<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daily Report: {{ $dailyReport->report_date->format('d M Y') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6 space-y-3 text-sm">
                <div><div class="text-gray-500">Teknisi</div><div class="font-medium">{{ $dailyReport->technician->name }}</div></div>
                <div><div class="text-gray-500">Aktivitas</div><div class="font-medium">{{ $dailyReport->activity }}</div></div>
                @if($dailyReport->ticket)
                    <div><div class="text-gray-500">Tiket</div><a href="{{ route('tickets.show', $dailyReport->ticket) }}" class="text-blue-600 hover:underline">{{ $dailyReport->ticket->ticket_number }}</a></div>
                @endif
                <div><div class="text-gray-500">Jam</div><div class="font-medium">{{ $dailyReport->start_time?->format('H:i') }} – {{ $dailyReport->end_time?->format('H:i') }}</div></div>
                <div><div class="text-gray-500">Lokasi</div><div class="font-medium">{{ $dailyReport->location ?? '-' }}</div></div>
                <hr>
                <div><div class="text-gray-500">Catatan Progress</div><div class="whitespace-pre-line">{{ $dailyReport->progress_note }}</div></div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('daily-reports.index') }}" class="px-3 py-1.5 bg-gray-200 rounded text-sm">Kembali</a>
                    @if($dailyReport->technician_id === auth()->id() || auth()->user()->isAdmin())
                        <a href="{{ route('daily-reports.edit', $dailyReport) }}" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm">Edit</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
