<x-admin-layout>
    <x-slot name="header">Gate {{ $gate->code }}</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.gates.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to gates</a>
        <div class="flex gap-2">
            <a href="{{ route('admin.gates.edit', $gate) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Edit</a>
            <form method="POST" action="{{ route('admin.gates.destroy', $gate) }}" onsubmit="return confirm('Delete this gate?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100">Delete</button>
            </form>
        </div>
    </div>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-gray-500">Gate Code</dt><dd class="mt-1 font-medium text-gray-800">{{ $gate->code }}</dd></div>
            <div><dt class="text-gray-500">Status</dt><dd class="mt-1"><x-status-badge :status="$gate->status" /></dd></div>
            <div><dt class="text-gray-500">Terminal</dt><dd class="mt-1 font-medium text-gray-800">{{ $gate->terminal?->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Airport</dt><dd class="mt-1 font-medium text-gray-800">{{ $gate->terminal?->airport?->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Gate Assignments</dt><dd class="mt-1 font-medium text-gray-800">{{ $gate->flight_gate_assignments_count }}</dd></div>
            <div><dt class="text-gray-500">Check-ins</dt><dd class="mt-1 font-medium text-gray-800">{{ $gate->check_ins_count }}</dd></div>
        </dl>
    </div>
</x-admin-layout>
