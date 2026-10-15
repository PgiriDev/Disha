<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="airport_id" value="Airport" />
        <select id="airport_id" name="airport_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
            <option value="">Select an airport</option>
            @foreach ($airports as $airport)
                <option value="{{ $airport->id }}" @selected((string) old('airport_id', $runway->airport_id ?? '') === (string) $airport->id)>{{ $airport->name }} ({{ $airport->iata_code }})</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('airport_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="designation" value="Designation" />
        <x-text-input id="designation" name="designation" class="mt-1 block w-full uppercase" maxlength="10" :value="old('designation', $runway->designation ?? '')" placeholder="e.g. 09/27" required autofocus />
        <x-input-error :messages="$errors->get('designation')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="surface" value="Surface" />
        <x-text-input id="surface" name="surface" class="mt-1 block w-full" :value="old('surface', $runway->surface ?? 'asphalt')" required />
        <x-input-error :messages="$errors->get('surface')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="length_m" value="Length (m)" />
        <x-text-input id="length_m" name="length_m" type="number" min="1" class="mt-1 block w-full" :value="old('length_m', $runway->length_m ?? '')" required />
        <x-input-error :messages="$errors->get('length_m')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="width_m" value="Width (m, optional)" />
        <x-text-input id="width_m" name="width_m" type="number" min="1" class="mt-1 block w-full" :value="old('width_m', $runway->width_m ?? '')" />
        <x-input-error :messages="$errors->get('width_m')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
            @foreach (['active', 'maintenance', 'closed'] as $status)
                <option value="{{ $status }}" @selected(old('status', $runway->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
</div>
