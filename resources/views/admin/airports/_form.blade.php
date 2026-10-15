<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="name" value="Airport Name" />
        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $airport->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="iata_code" value="IATA Code (3 letters)" />
        <x-text-input id="iata_code" name="iata_code" class="mt-1 block w-full uppercase" maxlength="3" :value="old('iata_code', $airport->iata_code ?? '')" required />
        <x-input-error :messages="$errors->get('iata_code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="icao_code" value="ICAO Code (4 letters)" />
        <x-text-input id="icao_code" name="icao_code" class="mt-1 block w-full uppercase" maxlength="4" :value="old('icao_code', $airport->icao_code ?? '')" required />
        <x-input-error :messages="$errors->get('icao_code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="city" value="City" />
        <x-text-input id="city" name="city" class="mt-1 block w-full" :value="old('city', $airport->city ?? '')" required />
        <x-input-error :messages="$errors->get('city')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="country" value="Country" />
        <x-text-input id="country" name="country" class="mt-1 block w-full" :value="old('country', $airport->country ?? '')" required />
        <x-input-error :messages="$errors->get('country')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="timezone" value="Timezone" />
        <x-text-input id="timezone" name="timezone" class="mt-1 block w-full" :value="old('timezone', $airport->timezone ?? '')" placeholder="e.g. Asia/Colombo" required />
        <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="latitude" value="Latitude (optional)" />
        <x-text-input id="latitude" name="latitude" type="number" step="0.0000001" class="mt-1 block w-full" :value="old('latitude', $airport->latitude ?? '')" />
        <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="longitude" value="Longitude (optional)" />
        <x-text-input id="longitude" name="longitude" type="number" step="0.0000001" class="mt-1 block w-full" :value="old('longitude', $airport->longitude ?? '')" />
        <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="elevation_ft" value="Elevation (ft, optional)" />
        <x-text-input id="elevation_ft" name="elevation_ft" type="number" class="mt-1 block w-full" :value="old('elevation_ft', $airport->elevation_ft ?? '')" />
        <x-input-error :messages="$errors->get('elevation_ft')" class="mt-2" />
    </div>

    <div class="flex items-end">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $airport->is_active ?? true))
                   class="rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500">
            <span class="text-sm text-gray-700">Active</span>
        </label>
    </div>
</div>
