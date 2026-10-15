<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <x-input-label for="airline_id" value="Airline" />
        <select id="airline_id" name="airline_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
            <option value="">Select an airline</option>
            @foreach ($airlines as $airline)
                <option value="{{ $airline->id }}" @selected((string) old('airline_id', $aircraft->airline_id ?? '') === (string) $airline->id)>{{ $airline->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('airline_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="registration_number" value="Registration Number" />
        <x-text-input id="registration_number" name="registration_number" class="mt-1 block w-full uppercase" maxlength="10" :value="old('registration_number', $aircraft->registration_number ?? '')" required />
        <x-input-error :messages="$errors->get('registration_number')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="manufacturer" value="Manufacturer" />
        <x-text-input id="manufacturer" name="manufacturer" class="mt-1 block w-full" :value="old('manufacturer', $aircraft->manufacturer ?? '')" required />
        <x-input-error :messages="$errors->get('manufacturer')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="model" value="Model" />
        <x-text-input id="model" name="model" class="mt-1 block w-full" :value="old('model', $aircraft->model ?? '')" required />
        <x-input-error :messages="$errors->get('model')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="seat_capacity" value="Total Seat Capacity" />
        <x-text-input id="seat_capacity" name="seat_capacity" type="number" min="1" class="mt-1 block w-full" :value="old('seat_capacity', $aircraft->seat_capacity ?? '')" required />
        <x-input-error :messages="$errors->get('seat_capacity')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
            @foreach (['active', 'maintenance', 'retired'] as $status)
                <option value="{{ $status }}" @selected(old('status', $aircraft->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="business_capacity" value="Business Capacity" />
        <x-text-input id="business_capacity" name="business_capacity" type="number" min="0" class="mt-1 block w-full" :value="old('business_capacity', $aircraft->business_capacity ?? 0)" />
        <x-input-error :messages="$errors->get('business_capacity')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="economy_capacity" value="Economy Capacity" />
        <x-text-input id="economy_capacity" name="economy_capacity" type="number" min="0" class="mt-1 block w-full" :value="old('economy_capacity', $aircraft->economy_capacity ?? 0)" />
        <x-input-error :messages="$errors->get('economy_capacity')" class="mt-2" />
    </div>
</div>
