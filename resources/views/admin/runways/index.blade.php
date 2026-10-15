<x-admin-layout>
    <x-slot name="header">Runways</x-slot>

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Runways</h2>
            <p class="text-sm text-gray-500">Manage runways for each airport.</p>
        </div>
        <a href="{{ route('admin.runways.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            New Runway
        </a>
    </div>

    <form method="GET" action="{{ route('admin.runways.index') }}" class="mb-4 flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex-1 min-w-[160px]">
            <x-input-label for="search" value="Search" />
            <x-text-input id="search" name="search" class="mt-1 block w-full" :value="request('search')" placeholder="Runway designation" />
        </div>
        <div class="w-56">
            <x-input-label for="airport" value="Airport" />
            <select id="airport" name="airport" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">All airports</option>
                @foreach ($airports as $airport)
                    <option value="{{ $airport->id }}" @selected((string) request('airport') === (string) $airport->id)>{{ $airport->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-40">
            <x-input-label for="status" value="Status" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">All</option>
                @foreach (['active', 'maintenance', 'closed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <x-primary-button>Filter</x-primary-button>
            <a href="{{ route('admin.runways.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        @if ($runways->isEmpty())
            <div class="px-5 py-12 text-center">
                <p class="text-sm font-medium text-gray-600">No runways found</p>
                <p class="mt-1 text-xs text-gray-400">Add runways to an airport.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Designation</th>
                            <th class="px-5 py-3">Airport</th>
                            <th class="px-5 py-3 text-right">Length</th>
                            <th class="px-5 py-3 text-right">Width</th>
                            <th class="px-5 py-3">Surface</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($runways as $runway)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-800">{{ $runway->designation }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $runway->airport?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-right text-gray-600">{{ number_format($runway->length_m) }} m</td>
                                <td class="px-5 py-3 text-right text-gray-600">{{ $runway->width_m !== null ? number_format($runway->width_m).' m' : '—' }}</td>
                                <td class="px-5 py-3 text-gray-600 capitalize">{{ $runway->surface }}</td>
                                <td class="px-5 py-3"><x-status-badge :status="$runway->status" /></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.runways.show', $runway) }}" class="text-sm font-medium text-sky-600 hover:text-sky-800">View</a>
                                        <a href="{{ route('admin.runways.edit', $runway) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Edit</a>
                                        <form method="POST" action="{{ route('admin.runways.destroy', $runway) }}" onsubmit="return confirm('Delete this runway?');">
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
            <div class="border-t border-gray-100 px-5 py-3">{{ $runways->links() }}</div>
        @endif
    </div>
</x-admin-layout>
