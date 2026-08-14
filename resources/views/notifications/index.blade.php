<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notifikasi</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow divide-y divide-gray-100">
                @forelse($notifications as $n)
                    <a href="{{ $n->data['url'] ?? '#' }}" class="block p-4 hover:bg-gray-50 {{ $n->read_at ? 'opacity-60' : '' }}">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">{{ $n->data['icon'] ?? '🔔' }}</span>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">{{ $n->data['title'] ?? 'Notifikasi' }}</div>
                                <div class="text-sm text-gray-600">{{ $n->data['body'] ?? '' }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</div>
                            </div>
                            @if(! $n->read_at)
                                <span class="w-2 h-2 mt-2 rounded-full bg-blue-500"></span>
                            @endif
                        </div>
                    </a>
                @empty
                    <p class="p-6 text-center text-gray-500">Tidak ada notifikasi.</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $notifications->links() }}</div>
        </div>
    </div>
</x-app-layout>
