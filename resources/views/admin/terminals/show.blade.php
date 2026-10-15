<x-admin-layout>
    <x-slot name="header">{{ $terminal->name }}</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.terminals.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to terminals</a>
        <div class="flex gap-2">
            <a href="{{ route('admin.terminals.edit', $terminal) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Edit</a>
            <form method="POST" action="{{ route('admin.terminals.destroy', $terminal) }}" onsubmit="return confirm('Delete this terminal?');">
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
                <div class="flex justify-between"><dt class="text-gray-500">Code</dt><dd class="font-medium text-gray-800">{{ $terminal->code }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Airport</dt><dd class="font-medium text-gray-800">{{ $terminal->airport?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Capacity</dt><dd class="font-medium text-gray-800">{{ $terminal->passenger_capacity !== null ? number_format($terminal->passenger_capacity) : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Gates</dt><dd class="font-medium text-gray-800">{{ $terminal->gates_count }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Gates</h2>
            @if ($gates->isEmpty())
                <p class="py-4 text-center text-sm text-gray-400">No gates registered in this terminal.</p>
            @else
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($gates as $gate)
                        <li class="flex items-center justify-between py-2">
                            <span class="font-medium text-gray-800">{{ $gate->code }}</span>
                            <x-status-badge :status="$gate->status" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-admin-layout>
