<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan & Statistik</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('reports.index') }}" class="bg-white p-4 rounded shadow mb-4 flex gap-2 items-end text-sm">
                <div>
                    <label class="block text-xs text-gray-500">Dari</label>
                    <input type="date" name="from" value="{{ $from }}" class="mt-1 border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Sampai</label>
                    <input type="date" name="to" value="{{ $to }}" class="mt-1 border-gray-300 rounded-md">
                </div>
                <button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Terapkan</button>
            </form>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-xs text-gray-500 uppercase">Rata-rata Rating</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $avgRating ? number_format($avgRating, 1) : '—' }}</div>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-xs text-gray-500 uppercase">Open / Progress</div>
                    <div class="text-3xl font-bold text-orange-600">{{ $slaOpen }}</div>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-xs text-gray-500 uppercase">Selesai</div>
                    <div class="text-3xl font-bold text-green-600">{{ $slaFinished }}</div>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-xs text-gray-500 uppercase">Overdue</div>
                    <div class="text-3xl font-bold text-red-600">{{ $overdue }}</div>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-xs text-gray-500 uppercase">Daily Reports</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $reportsCount }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded shadow p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">By Status</h3>
                    <canvas id="statusChart" height="200"></canvas>
                </div>
                <div class="bg-white rounded shadow p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">By Priority</h3>
                    <canvas id="priorityChart" height="200"></canvas>
                </div>
            </div>

            <div class="bg-white rounded shadow p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">Tren Harian</h3>
                <canvas id="trendChart" height="80"></canvas>
            </div>

            <div class="bg-white rounded shadow p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">Tiket per Kategori</h3>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byCategory as $catId => $total)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $categories[$catId]->name ?? 'Tanpa Kategori' }}</td>
                                <td class="px-4 py-2 text-right font-medium">{{ $total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-3">Performa Teknisi</h3>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Teknisi</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Selesai</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($technicianStats as $row)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $row['t']->name }}</td>
                                <td class="px-4 py-2 text-right">{{ $row['total'] }}</td>
                                <td class="px-4 py-2 text-right text-green-700">{{ $row['finished'] }}</td>
                                <td class="px-4 py-2 text-right text-blue-700">{{ $row['open'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const statusData = @json($byStatus);
        const priorityData = @json($byPriority);
        const trendData = @json($dailyTrend);

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData).map(s => ({
                    'open':'Open','forwarded':'Forwarded','assigned':'Assigned','in_progress':'In Progress',
                    'finished':'Finished','verified':'Verified','closed':'Closed','reopened':'Reopened','cancelled':'Cancelled'
                })[s] || s),
                datasets: [{ data: Object.values(statusData), backgroundColor: ['#6b7280','#3b82f6','#6366f1','#eab308','#22c55e','#10b981','#52525b','#f97316','#ef4444'] }]
            }
        });

        new Chart(document.getElementById('priorityChart'), {
            type: 'bar',
            data: {
                labels: ['Low','Medium','High','Urgent'].map(p => p.charAt(0).toUpperCase() + p.slice(1)),
                datasets: [{ data: ['low','medium','high','urgent'].map(k => priorityData[k] || 0), backgroundColor: ['#9ca3af','#3b82f6','#f97316','#ef4444'] }]
            },
            options: { plugins: { legend: { display: false } } }
        });

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: Object.keys(trendData),
                datasets: [{ label: 'Tiket', data: Object.values(trendData), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', fill: true, tension: 0.3 }]
            }
        });
    </script>
</x-app-layout>
