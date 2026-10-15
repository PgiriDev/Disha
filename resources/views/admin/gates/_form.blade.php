<div class="grid grid-cols-1 gap-5">
    <div>
        <x-input-label for="terminal_id" value="Terminal" />
        <select id="terminal_id" name="terminal_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
            <option value="">Select a terminal</option>
            @foreach ($terminals as $terminal)
                <option value="{{ $terminal->id }}" @selected((string) old('terminal_id', $gate->terminal_id ?? '') === (string) $terminal->id)>
                    {{ $terminal->airport?->name }} &mdash; {{ $terminal->name }} ({{ $terminal->code }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('terminal_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="code" value="Gate Code" />
        <x-text-input id="code" name="code" class="mt-1 block w-full uppercase" maxlength="10" :value="old('code', $gate->code ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
            @foreach (['available', 'occupied', 'closed'] as $status)
                <option value="{{ $status }}" @selected(old('status', $gate->status ?? 'available') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
</div>
