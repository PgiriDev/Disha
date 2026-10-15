<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="airport_id" value="Airport" />
        <select id="airport_id" name="airport_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
            <option value="">Select an airport</option>
            @foreach ($airports as $airport)
                <option value="{{ $airport->id }}" @selected((string) old('airport_id', $terminal->airport_id ?? '') === (string) $airport->id)>{{ $airport->name }} ({{ $airport->iata_code }})</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('airport_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="name" value="Terminal Name" />
        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $terminal->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="code" value="Code" />
        <x-text-input id="code" name="code" class="mt-1 block w-full uppercase" maxlength="10" :value="old('code', $terminal->code ?? '')" required />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="passenger_capacity" value="Passenger Capacity (optional)" />
        <x-text-input id="passenger_capacity" name="passenger_capacity" type="number" min="0" class="mt-1 block w-full" :value="old('passenger_capacity', $terminal->passenger_capacity ?? '')" />
        <x-input-error :messages="$errors->get('passenger_capacity')" class="mt-2" />
    </div>
</div>
