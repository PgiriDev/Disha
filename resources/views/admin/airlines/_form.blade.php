<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <x-input-label for="name" value="Airline Name" />
        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $airline->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="country" value="Country" />
        <x-text-input id="country" name="country" class="mt-1 block w-full" :value="old('country', $airline->country ?? '')" required />
        <x-input-error :messages="$errors->get('country')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="iata_code" value="IATA Code (2 letters)" />
        <x-text-input id="iata_code" name="iata_code" class="mt-1 block w-full uppercase" maxlength="2" :value="old('iata_code', $airline->iata_code ?? '')" required />
        <x-input-error :messages="$errors->get('iata_code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="icao_code" value="ICAO Code (3 letters)" />
        <x-text-input id="icao_code" name="icao_code" class="mt-1 block w-full uppercase" maxlength="3" :value="old('icao_code', $airline->icao_code ?? '')" required />
        <x-input-error :messages="$errors->get('icao_code')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="callsign" value="Callsign (optional)" />
        <x-text-input id="callsign" name="callsign" class="mt-1 block w-full" :value="old('callsign', $airline->callsign ?? '')" />
        <x-input-error :messages="$errors->get('callsign')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $airline->is_active ?? true))
                   class="rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500">
            <span class="text-sm text-gray-700">Active</span>
        </label>
        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
    </div>
</div>
