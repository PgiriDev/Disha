<x-admin-layout>
    <x-slot name="header">{{ $airline->name }}</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.airlines.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to airlines</a>
        <div class="flex gap-2">
            <a href="{{ route('admin.airlines.edit', $airline) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Edit</a>
            <form method="POST" action="{{ route('admin.airlines.destroy', $airline) }}" onsubmit="return confirm('Delete this airline?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-1">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Details</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">IATA</dt><dd class="font-medium text-gray-800">{{ $airline->iata_code }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">ICAO</dt><dd class="font-medium text-gray-800">{{ $airline->icao_code }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Country</dt><dd class="font-medium text-gray-800">{{ $airline->country }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Callsign</dt><dd class="font-medium text-gray-800">{{ $airline->callsign ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$airline->is_active ? 'active' : 'closed'" /></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Aircraft</dt><dd class="font-medium text-gray-800">{{ $airline->aircraft_count }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Flights</dt><dd class="font-medium text-gray-800">{{ $airline->flights_count }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Fleet</h2>
            @if ($aircraft->isEmpty())
                <p class="py-6 text-center text-sm text-gray-400">No aircraft assigned to this airline.</p>
            @else
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="pb-2">Registration</th>
                            <th class="pb-2">Type</th>
                            <th class="pb-2">Seats</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($aircraft as $item)
                            <tr>
                                <td class="py-2 font-medium text-gray-800">{{ $item->registration_number }}</td>
                                <td class="py-2 text-gray-600">{{ $item->manufacturer }} {{ $item->model }}</td>
                                <td class="py-2 text-gray-600">{{ $item->seat_capacity }}</td>
                                <td class="py-2"><x-status-badge :status="$item->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">{{ $aircraft->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
