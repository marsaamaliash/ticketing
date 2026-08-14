<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit {{ $customer->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6">
                <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('customers._form')
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('customers.show', $customer) }}" class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
