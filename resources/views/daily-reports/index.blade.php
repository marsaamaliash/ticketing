<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daily Reports</h2>
            <a href="{{ route('daily-reports.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded text-sm">+ Daily Report</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($today->isNotEmpty())
                <div class="bg-white rounded shadow p-6 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Hari Ini ({{ $today->count() }} laporan)</h3>
                    <ul class="space-y-2 text-sm">
                        @foreach($today as $r)
                            <li class="border-l-4 border-lime-400 pl-3">
                                <a href="{{ route('daily-reports.show', $r) }}" class="font-medium text-gray-800 hover:underline">{{ $r->activity }}</a>
                                <div class="text-xs text-gray-500">
                                    @if($r->ticket){{ $r->ticket->ticket_number }} • @endif
                                    {{ $r->start_time?->format('H:i') }}–{{ $r->end_time?->format('H:i') }}
                                    @if($r->location) • {{ $r->location }} @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktivitas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiket</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reports as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $r->report_date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $r->activity }}</td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-600">{{ $r->ticket?->ticket_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $r->location ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <a href="{{ route('daily-reports.show', $r) }}" class="text-blue-600 hover:underline">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-gray-500 py-6">Belum ada daily report.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-3">{{ $reports->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
