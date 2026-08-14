<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $ticket->ticket_number }}</h2>
                <div class="text-sm text-gray-500">{{ $ticket->title }}</div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tickets.pdf.surat', $ticket) }}" target="_blank" class="px-3 py-1.5 bg-purple-600 text-white rounded text-sm">📄 Cetak Surat</a>
                <a href="{{ route('tickets.index') }}" class="px-3 py-1.5 bg-gray-200 rounded text-sm">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded shadow p-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="text-sm text-gray-500">Pelanggan</div>
                                <a href="{{ route('customers.show', $ticket->customer) }}" class="text-lg font-semibold text-gray-800 hover:underline">{{ $ticket->customer->name }}</a>
                                <div class="text-xs text-gray-500 font-mono">{{ $ticket->customer->customer_code }} • {{ $ticket->customer->phone }}</div>
                                <div class="text-xs text-gray-500">{{ $ticket->customer->address }}</div>
                            </div>
                            <div class="flex flex-col gap-2 items-end">
                                <x-status-badge :status="$ticket->status"/>
                                <x-priority-badge :priority="$ticket->priority"/>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="text-sm font-medium text-gray-700 mb-1">Deskripsi</div>
                        <div class="text-sm text-gray-700 whitespace-pre-line">{{ $ticket->description }}</div>

                        <hr class="my-3">

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                            <div><div class="text-gray-500">Kategori</div><div class="font-medium">{{ $ticket->category->name }}</div></div>
                            <div><div class="text-gray-500">Dibuat oleh</div><div class="font-medium">{{ $ticket->creator->name }}</div></div>
                            <div><div class="text-gray-500">Tgl Dibuat</div><div class="font-medium">{{ $ticket->created_at->format('d M Y H:i') }}</div></div>
                            <div><div class="text-gray-500">Teknisi</div><div class="font-medium">{{ $ticket->technician?->name ?? '-' }}</div></div>
                            @if($ticket->scheduled_at)
                                <div><div class="text-gray-500">Jadwal</div><div class="font-medium">{{ $ticket->scheduled_at->format('d M Y H:i') }}</div></div>
                            @endif
                            @if($ticket->started_at)
                                <div><div class="text-gray-500">Mulai</div><div class="font-medium">{{ $ticket->started_at->format('d M Y H:i') }}</div></div>
                            @endif
                            @if($ticket->finished_at)
                                <div><div class="text-gray-500">Selesai</div><div class="font-medium">{{ $ticket->finished_at->format('d M Y H:i') }}</div></div>
                            @endif
                            @if($ticket->rating)
                                <div><div class="text-gray-500">Rating</div><div class="font-medium">{{ $ticket->rating }}/5 ★</div></div>
                            @endif
                        </div>
                    </div>

                    @if($ticket->devices->isNotEmpty())
                        <div class="bg-white rounded shadow p-6">
                            <h3 class="font-semibold text-gray-800 mb-3">Perangkat</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($ticket->devices as $d)
                                    <div class="border rounded p-3 text-sm bg-gray-50">
                                        <div class="font-mono text-xs text-gray-500">{{ $ticket->ticket_number }}</div>
                                        <div class="font-medium">{{ $d->device_type ?? 'Device' }} {{ $d->brand }} {{ $d->model }}</div>
                                        <div class="text-xs text-gray-600">SN: <span class="font-mono">{{ $d->serial_number ?? '-' }}</span></div>
                                        <div class="text-xs text-gray-600">Lokasi: {{ $d->location ?? '-' }}</div>
                                        @if($d->installed_at)
                                            <div class="text-xs text-gray-600">Tgl Instalasi: {{ $d->installed_at->format('d M Y') }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="bg-white rounded shadow p-6">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold text-gray-800">Lampiran ({{ $ticket->attachments->count() }})</h3>
                        </div>
                        <div class="space-y-2 mb-4">
                            @forelse($ticket->attachments as $att)
                                <div class="flex items-center justify-between border rounded p-2 text-sm">
                                    <div>
                                        <div class="font-medium">{{ $att->original_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $att->size_kb }} KB • oleh {{ $att->uploader->name }} • {{ $att->created_at->format('d M Y H:i') }}</div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('tickets.attachments.download', [$ticket, $att]) }}" class="text-blue-600 hover:underline text-sm">Download</a>
                                        @can('delete', $att)
                                            <form method="POST" action="{{ route('tickets.attachments.destroy', [$ticket, $att]) }}" onsubmit="return confirm('Hapus lampiran ini?')">
                                                @csrf @method('DELETE')
                                                <button class="text-red-600 hover:underline text-sm">Hapus</button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">Belum ada lampiran.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('tickets.attachments.store', $ticket) }}" enctype="multipart/form-data" class="border-t pt-3">
                            @csrf
                            <div class="flex gap-2 items-center">
                                <input type="file" name="files[]" multiple accept="image/*,application/pdf" class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Upload</button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF. Maks 5MB per file.</p>
                        </form>
                    </div>

                    @if(auth()->user()->can('inputDiagnosis', $ticket) && ($ticket->status === 'assigned' || $ticket->status === 'in_progress'))
                        <div class="bg-yellow-50 border border-yellow-200 rounded shadow p-6">
                            <h3 class="font-semibold text-gray-800 mb-3">Diagnosis Saya</h3>
                            @php $myDiag = $ticket->diagnoses->firstWhere('technician_id', auth()->id()); @endphp
                            <form method="POST" action="{{ route('tickets.diagnosis.store', $ticket) }}" class="space-y-3 text-sm">
                                @csrf
                                <div>
                                    <label class="block text-xs text-gray-500">Diagnosis *</label>
                                    <textarea name="diagnosis_text" rows="2" required class="block mt-1 w-full border-gray-300 rounded-md">{{ $myDiag->diagnosis_text ?? '' }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500">Penyebab</label>
                                        <textarea name="root_cause" rows="2" class="block mt-1 w-full border-gray-300 rounded-md">{{ $myDiag->root_cause ?? '' }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500">Tindakan</label>
                                        <textarea name="action_taken" rows="2" class="block mt-1 w-full border-gray-300 rounded-md">{{ $myDiag->action_taken ?? '' }}</textarea>
                                    </div>
                                </div>
                                <button type="submit" class="px-3 py-2 bg-yellow-600 text-white rounded text-sm">Simpan Diagnosis</button>
                            </form>
                        </div>
                    @elseif($ticket->diagnoses->isNotEmpty())
                        <div class="bg-white rounded shadow p-6">
                            <h3 class="font-semibold text-gray-800 mb-3">Diagnosis</h3>
                            @foreach($ticket->diagnoses as $d)
                                <div class="border-l-4 border-yellow-400 pl-3 mb-2">
                                    <div class="text-sm font-medium">{{ $d->technician->name }} • {{ $d->created_at->format('d M Y H:i') }}</div>
                                    <div class="text-sm text-gray-700 mt-1">{{ $d->diagnosis_text }}</div>
                                    @if($d->root_cause)
                                        <div class="text-xs text-gray-600 mt-1"><strong>Penyebab:</strong> {{ $d->root_cause }}</div>
                                    @endif
                                    @if($d->action_taken)
                                        <div class="text-xs text-gray-600"><strong>Tindakan:</strong> {{ $d->action_taken }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="bg-white rounded shadow p-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Aktivitas ({{ $ticket->activities->count() }})</h3>

                        <form method="POST" action="{{ route('tickets.comment', $ticket) }}" class="mb-4">
                            @csrf
                            <textarea name="description" rows="2" required placeholder="Tulis komentar..." class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                            <div class="mt-2 text-right">
                                <button class="px-3 py-1.5 bg-gray-700 text-white rounded text-sm">Kirim Komentar</button>
                            </div>
                        </form>

                        <div class="space-y-3 border-t pt-3">
                            @forelse($ticket->activities as $a)
                                <div class="flex items-start gap-3 text-sm">
                                    <span class="text-xs px-2 py-0.5 rounded bg-{{ $a->type_color }}-100 text-{{ $a->type_color }}-700 flex-shrink-0">{{ $a->type_label }}</span>
                                    <div class="flex-1">
                                        <div>{{ $a->description }}</div>
                                        <div class="text-xs text-gray-500">{{ $a->user->name }} • {{ $a->created_at->format('d M Y H:i') }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">Belum ada aktivitas.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded shadow p-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Aksi</h3>

                        <div class="space-y-2">
                            @can('forward', $ticket)
                                <form method="POST" action="{{ route('tickets.forward', $ticket) }}">
                                    @csrf
                                    <textarea name="note" rows="2" placeholder="Catatan untuk manager (opsional)" class="block w-full border-gray-300 rounded-md text-sm mb-1"></textarea>
                                    <button class="w-full px-3 py-2 bg-blue-600 text-white rounded text-sm">Teruskan ke Manager</button>
                                </form>
                            @endcan

                            @can('assign', $ticket)
                                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="space-y-2">
                                    @csrf
                                    <select name="assigned_technician_id" required class="block w-full border-gray-300 rounded-md text-sm">
                                        <option value="">— Pilih teknisi —</option>
                                        @foreach(\App\Models\User::role('teknisi')->orderBy('name')->get() as $tech)
                                            <option value="{{ $tech->id }}" @selected($ticket->assigned_technician_id === $tech->id)>{{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="datetime-local" name="scheduled_at" value="{{ optional($ticket->scheduled_at)->format('Y-m-d\TH:i') }}" class="block w-full border-gray-300 rounded-md text-sm">
                                    <button class="w-full px-3 py-2 bg-indigo-600 text-white rounded text-sm">Assign Teknisi</button>
                                </form>
                            @endcan

                            @can('startProgress', $ticket)
                                <form method="POST" action="{{ route('tickets.start', $ticket) }}">
                                    @csrf
                                    <button class="w-full px-3 py-2 bg-yellow-600 text-white rounded text-sm">Mulai Pengerjaan</button>
                                </form>
                            @endcan

                            @can('markFinished', $ticket)
                                <form method="POST" action="{{ route('tickets.finish', $ticket) }}" class="space-y-2">
                                    @csrf
                                    <textarea name="action_taken" required rows="2" placeholder="Apa yang dilakukan? (wajib)" class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                                    <button class="w-full px-3 py-2 bg-green-600 text-white rounded text-sm">Tandai Selesai</button>
                                </form>
                            @endcan

                            @can('verify', $ticket)
                                <form method="POST" action="{{ route('tickets.verify', $ticket) }}" class="space-y-2">
                                    @csrf
                                    <textarea name="note" rows="2" placeholder="Catatan verifikasi (opsional)" class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                                    <button class="w-full px-3 py-2 bg-emerald-600 text-white rounded text-sm">Verifikasi (Saya cek sudah OK)</button>
                                </form>
                            @endcan

                            @can('rate', $ticket)
                                <form method="POST" action="{{ route('tickets.rate', $ticket) }}" class="space-y-2">
                                    @csrf
                                    <select name="rating" required class="block w-full border-gray-300 rounded-md text-sm">
                                        @for($i=1;$i<=5;$i++)
                                            <option value="{{ $i }}">{{ $i }} ★ — {{ ['Sangat Buruk','Buruk','Cukup','Baik','Sangat Baik'][$i-1] }}</option>
                                        @endfor
                                    </select>
                                    <textarea name="rating_comment" rows="2" placeholder="Komentar (opsional)" class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                                    <button class="w-full px-3 py-2 bg-emerald-700 text-white rounded text-sm">Beri Rating & Tutup Tiket</button>
                                </form>
                            @endcan

                            @can('reopen', $ticket)
                                <form method="POST" action="{{ route('tickets.reopen', $ticket) }}" class="space-y-2">
                                    @csrf
                                    <textarea name="reason" required rows="2" placeholder="Alasan reopen (wajib)" class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                                    <button class="w-full px-3 py-2 bg-orange-600 text-white rounded text-sm">Buka Ulang Tiket</button>
                                </form>
                            @endcan

                            @can('cancel', $ticket)
                                <form method="POST" action="{{ route('tickets.cancel', $ticket) }}" class="space-y-2">
                                    @csrf
                                    <textarea name="cancellation_reason" required rows="2" placeholder="Alasan batalkan (wajib)" class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                                    <button class="w-full px-3 py-2 bg-red-600 text-white rounded text-sm">Batalkan Tiket</button>
                                </form>
                            @endcan

                            @can('update', $ticket)
                                <a href="{{ route('tickets.edit', $ticket) }}" class="block w-full px-3 py-2 bg-gray-200 rounded text-sm text-center">Edit Data</a>
                            @endcan
                        </div>
                    </div>

                    <div class="bg-white rounded shadow p-6 text-sm">
                        <h3 class="font-semibold text-gray-800 mb-3">Riwayat Status</h3>
                        <ol class="space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="w-2 h-2 mt-1.5 rounded-full {{ in_array($ticket->status, ['open','forwarded','assigned','in_progress','finished','verified','closed','reopened'])?'bg-blue-500':'bg-gray-300' }}"></span>
                                <div><div class="font-medium">Open</div><div class="text-xs text-gray-500">{{ $ticket->created_at->format('d M Y H:i') }}</div></div>
                            </li>
                            @if(in_array($ticket->status, ['forwarded','assigned','in_progress','finished','verified','closed','reopened']))
                                <li class="flex items-start gap-2">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-blue-500"></span>
                                    <div><div class="font-medium">Forwarded ke Manager</div></div>
                                </li>
                            @endif
                            @if($ticket->assigned_technician_id)
                                <li class="flex items-start gap-2">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-indigo-500"></span>
                                    <div><div class="font-medium">Assigned ke {{ $ticket->technician?->name }}</div>@if($ticket->scheduled_at)<div class="text-xs text-gray-500">Jadwal {{ $ticket->scheduled_at->format('d M H:i') }}</div>@endif</div>
                                </li>
                            @endif
                            @if(in_array($ticket->status, ['in_progress','finished','verified','closed','reopened']))
                                <li class="flex items-start gap-2">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-yellow-500"></span>
                                    <div><div class="font-medium">In Progress</div>@if($ticket->started_at)<div class="text-xs text-gray-500">{{ $ticket->started_at->format('d M Y H:i') }}</div>@endif</div>
                                </li>
                            @endif
                            @if(in_array($ticket->status, ['finished','verified','closed','reopened']))
                                <li class="flex items-start gap-2">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-green-500"></span>
                                    <div><div class="font-medium">Finished</div>@if($ticket->finished_at)<div class="text-xs text-gray-500">{{ $ticket->finished_at->format('d M Y H:i') }}</div>@endif</div>
                                </li>
                            @endif
                            @if(in_array($ticket->status, ['verified','closed']))
                                <li class="flex items-start gap-2">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-emerald-500"></span>
                                    <div><div class="font-medium">Verified</div>@if($ticket->verified_at)<div class="text-xs text-gray-500">oleh {{ $ticket->verifier?->name }} • {{ $ticket->verified_at->format('d M Y H:i') }}</div>@endif</div>
                                </li>
                            @endif
                            @if($ticket->status === 'closed')
                                <li class="flex items-start gap-2">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-zinc-600"></span>
                                    <div><div class="font-medium">Closed</div>@if($ticket->rating)<div class="text-xs text-gray-500">Rating {{ $ticket->rating }}/5</div>@endif</div>
                                </li>
                            @endif
                            @if($ticket->status === 'reopened')
                                <li class="flex items-start gap-2">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-orange-500"></span>
                                    <div><div class="font-medium">Reopened</div></div>
                                </li>
                            @endif
                            @if($ticket->status === 'cancelled')
                                <li class="flex items-start gap-2">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-red-500"></span>
                                    <div><div class="font-medium">Cancelled</div><div class="text-xs text-gray-500">{{ $ticket->cancellation_reason }}</div></div>
                                </li>
                            @endif
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
