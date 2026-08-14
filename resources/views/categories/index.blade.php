<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Kategori') }}</h2>
            @can('create', \App\Models\Category::class)
                <a href="{{ route('categories.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded text-sm">+ Kategori</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"># Tiket</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($categories as $c)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $c->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $c->slug }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $c->description }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $c->tickets_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    @can('update', $c)
                                        <a href="{{ route('categories.edit', $c) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
