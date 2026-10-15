<x-admin-layout>
    <x-slot name="header">Airports</x-slot>

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Airports</h2>
            <p class="text-sm text-gray-500">Manage the airports in the operations network.</p>
        </div>
        <a href="{{ route('admin.airports.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            New Airport
        </a>
    </div>

    <form method="GET" action="{{ route('admin.airports.index') }}" class="mb-4 flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex-1 min-w-[200px]">
            <x-input-label for="search" value="Search" />
            <x-text-input id="search" name="search" class="mt-1 block w-full" :value="request('search')" placeholder="Name, city, country or codes" />
        </div>
        <div class="w-40">
            <x-input-label for="status" value="Status" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">All</option>
                <option value="1" @selected(request('status') === '1')>Active</option>
                <option value="0" @selected(request('status') === '0')>Inactive</option>
            </select>
        </div>
        <div class="flex gap-2">
            <x-primary-button>Filter</x-primary-button>
            <a href="{{ route('admin.airports.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        @if ($airports->isEmpty())
            <div class="px-5 py-12 text-center">
                <p class="text-sm font-medium text-gray-600">No airports found</p>
                <p class="mt-1 text-xs text-gray-400">Create your first airport to get started.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Name</th>
                            <th class="px-5 py-3">IATA</th>
                            <th class="px-5 py-3">ICAO</th>
                            <th class="px-5 py-3">City</th>
                            <th class="px-5 py-3">Country</th>
                            <th class="px-5 py-3 text-center">Terminals</th>
                            <th class="px-5 py-3 text-center">Runways</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($airports as $airport)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-800">{{ $airport->name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $airport->iata_code }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $airport->icao_code }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $airport->city }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $airport->country }}</td>
                                <td class="px-5 py-3 text-center text-gray-600">{{ $airport->terminals_count }}</td>
                                <td class="px-5 py-3 text-center text-gray-600">{{ $airport->runways_count }}</td>
                                <td class="px-5 py-3"><x-status-badge :status="$airport->is_active ? 'active' : 'closed'" /></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.airports.show', $airport) }}" class="text-sm font-medium text-sky-600 hover:text-sky-800">View</a>
                                        <a href="{{ route('admin.airports.edit', $airport) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Edit</a>
                                        <form method="POST" action="{{ route('admin.airports.destroy', $airport) }}" onsubmit="return confirm('Delete this airport?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-5 py-3">{{ $airports->links() }}</div>
        @endif
    </div>
</x-admin-layout>
