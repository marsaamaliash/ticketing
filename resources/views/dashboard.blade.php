<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }} — {{ Auth::user()->name }} ({{ Auth::user()->role_list }})
            </h2>
            @can('create', \App\Models\Ticket::class)
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300">
                    + Buat Tiket
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-xs text-gray-500 uppercase">Total</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-xs text-gray-500 uppercase">Open</div>
                    <div class="text-2xl font-bold text-gray-600">{{ $stats['open'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-xs text-gray-500 uppercase">Forwarded</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['forwarded'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-xs text-gray-500 uppercase">In Progress</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['in_progress'] + $stats['assigned'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-xs text-gray-500 uppercase">Finished</div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['finished'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-xs text-gray-500 uppercase">Overdue</div>
                    <div class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-800">
                            @if(auth()->user()->hasRole('cs'))
                                Tiket yang perlu tindakan Anda
                            @elseif(auth()->user()->hasRole('manager'))
                                Tiket yang perlu di-assign / dijadwalkan
                            @elseif(auth()->user()->hasRole('teknisi'))
                                Tiket yang harus saya kerjakan
                            @else
                                Tiket terbaru
                            @endif
                        </h3>
                        <a href="{{ route('tickets.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua →</a>
                    </div>
                    @if($myTasks->isEmpty())
                        <p class="text-gray-500 text-sm py-4">Tidak ada tiket aktif.</p>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach($myTasks as $t)
                                <a href="{{ route('tickets.show', $t) }}" class="block py-3 hover:bg-gray-50 -mx-3 px-3 rounded">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="text-sm font-mono text-gray-500">{{ $t->ticket_number }}</div>
                                            <div class="text-sm font-medium text-gray-800">{{ $t->title }}</div>
                                            <div class="text-xs text-gray-500">{{ $t->customer->name }} • {{ $t->customer->phone }}</div>
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="text-xs px-2 py-1 rounded bg-{{ $t->status_color }}-100 text-{{ $t->status_color }}-700">{{ $t->status_label }}</span>
                                            <span class="text-xs px-2 py-1 rounded bg-{{ $t->priority_color }}-100 text-{{ $t->priority_color }}-700">{{ ucfirst($t->priority) }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Jadwal Hari Ini</h3>
                    @if($todaySchedule->isEmpty())
                        <p class="text-gray-500 text-sm py-4">Tidak ada jadwal hari ini.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($todaySchedule as $t)
                                <a href="{{ route('tickets.show', $t) }}" class="block p-3 bg-gray-50 rounded hover:bg-gray-100">
                                    <div class="flex justify-between">
                                        <div class="text-sm font-medium text-gray-800">{{ $t->ticket_number }}</div>
                                        <div class="text-xs text-gray-500">{{ $t->scheduled_at?->format('H:i') }}</div>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $t->customer->name }} • {{ $t->category->name }}</div>
                                    <div class="text-xs text-gray-400">Teknisi: {{ $t->technician?->name ?? '-' }}</div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if(! $technicians->isEmpty())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Beban Kerja Teknisi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @foreach($technicians as $t)
                            <a href="{{ route('tickets.index', ['technician_id' => $t->id, 'status' => 'assigned']) }}" class="block p-3 border rounded hover:bg-gray-50">
                                <div class="text-sm font-medium text-gray-800">{{ $t->name }}</div>
                                <div class="text-xs text-gray-500">{{ $t->open_count }} tiket aktif</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
                @if($latestActivities->isEmpty())
                    <p class="text-gray-500 text-sm py-4">Belum ada aktivitas.</p>
                @else
                    <div class="space-y-2">
                        @foreach($latestActivities as $a)
                            <div class="flex items-start gap-3 text-sm">
                                <span class="text-xs px-2 py-0.5 rounded bg-{{ $a->type_color }}-100 text-{{ $a->type_color }}-700 flex-shrink-0 mt-0.5">{{ $a->type_label }}</span>
                                <div class="flex-1">
                                    <a href="{{ route('tickets.show', $a->ticket) }}" class="text-blue-600 hover:underline font-mono text-xs">{{ $a->ticket?->ticket_number }}</a>
                                    <span class="text-gray-500">— {{ $a->description }}</span>
                                </div>
                                <span class="text-xs text-gray-400">{{ $a->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
