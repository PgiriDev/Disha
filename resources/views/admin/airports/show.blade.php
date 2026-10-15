<x-admin-layout>
    <x-slot name="header">{{ $airport->name }}</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.airports.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to airports</a>
        <div class="flex gap-2">
            <a href="{{ route('admin.airports.edit', $airport) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Edit</a>
            <form method="POST" action="{{ route('admin.airports.destroy', $airport) }}" onsubmit="return confirm('Delete this airport?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Details</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">IATA</dt><dd class="font-medium text-gray-800">{{ $airport->iata_code }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">ICAO</dt><dd class="font-medium text-gray-800">{{ $airport->icao_code }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">City</dt><dd class="font-medium text-gray-800">{{ $airport->city }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Country</dt><dd class="font-medium text-gray-800">{{ $airport->country }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Timezone</dt><dd class="font-medium text-gray-800">{{ $airport->timezone }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Elevation</dt><dd class="font-medium text-gray-800">{{ $airport->elevation_ft !== null ? $airport->elevation_ft.' ft' : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$airport->is_active ? 'active' : 'closed'" /></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Departures</dt><dd class="font-medium text-gray-800">{{ $airport->departure_schedules_count }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Arrivals</dt><dd class="font-medium text-gray-800">{{ $airport->arrival_schedules_count }}</dd></div>
            </dl>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Terminals</h2>
                @if ($terminals->isEmpty())
                    <p class="py-4 text-center text-sm text-gray-400">No terminals registered.</p>
                @else
                    <ul class="divide-y divide-gray-100 text-sm">
                        @foreach ($terminals as $terminal)
                            <li class="flex items-center justify-between py-2">
                                <span class="font-medium text-gray-800">{{ $terminal->name }} <span class="text-gray-400">({{ $terminal->code }})</span></span>
                                <span class="text-gray-500">{{ $terminal->gates_count }} gates</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Runways</h2>
                @if ($runways->isEmpty())
                    <p class="py-4 text-center text-sm text-gray-400">No runways registered.</p>
                @else
                    <ul class="divide-y divide-gray-100 text-sm">
                        @foreach ($runways as $runway)
                            <li class="flex items-center justify-between py-2">
                                <span class="font-medium text-gray-800">{{ $runway->designation }}</span>
                                <span class="flex items-center gap-3 text-gray-500">
                                    {{ number_format($runway->length_m) }} m
                                    <x-status-badge :status="$runway->status" />
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
