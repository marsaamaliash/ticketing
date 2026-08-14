@csrf
<div class="space-y-4">
    <div>
        <x-input-label for="name" value="Nama Kategori *" />
        <input id="name" name="name" type="text" value="{{ old('name', $category->name ?? '') }}" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">
        <x-input-error :messages="$errors->get('name')" class="mt-1"/>
    </div>
    <div>
        <x-input-label for="description" value="Deskripsi" />
        <textarea id="description" name="description" rows="2" class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('description', $category->description ?? '') }}</textarea>
    </div>
</div>
