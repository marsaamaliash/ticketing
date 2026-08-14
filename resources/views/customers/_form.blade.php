@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" value="Nama Pelanggan *" />
        <input id="name" name="name" type="text" value="{{ old('name', $customer->name ?? '') }}" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">
        <x-input-error :messages="$errors->get('name')" class="mt-1"/>
    </div>
    <div>
        <x-input-label for="phone" value="No. Telepon *" />
        <input id="phone" name="phone" type="text" value="{{ old('phone', $customer->phone ?? '') }}" required class="block mt-1 w-full border-gray-300 rounded-md text-sm">
        <x-input-error :messages="$errors->get('phone')" class="mt-1"/>
    </div>
    <div>
        <x-input-label for="email" value="Email" />
        <input id="email" name="email" type="email" value="{{ old('email', $customer->email ?? '') }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
        <x-input-error :messages="$errors->get('email')" class="mt-1"/>
    </div>
    <div>
        <x-input-label for="city" value="Kota" />
        <input id="city" name="city" type="text" value="{{ old('city', $customer->city ?? '') }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
    </div>
    <div class="md:col-span-2">
        <x-input-label for="address" value="Alamat" />
        <textarea id="address" name="address" rows="2" class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>
    <div>
        <x-input-label for="latitude" value="Latitude (opsional)" />
        <input id="latitude" name="latitude" type="text" value="{{ old('latitude', $customer->latitude ?? '') }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
    </div>
    <div>
        <x-input-label for="longitude" value="Longitude (opsional)" />
        <input id="longitude" name="longitude" type="text" value="{{ old('longitude', $customer->longitude ?? '') }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
    </div>
    <div class="md:col-span-2">
        <x-input-label for="notes" value="Catatan" />
        <textarea id="notes" name="notes" rows="2" class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('notes', $customer->notes ?? '') }}</textarea>
    </div>
</div>
