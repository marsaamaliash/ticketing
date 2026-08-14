<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Pelanggan Baru') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6">
                <form method="POST" action="{{ route('customers.store') }}" class="space-y-4">
                    @csrf
                    @include('customers._form')
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
