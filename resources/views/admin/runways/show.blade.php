<x-admin-layout>
    <x-slot name="header">Runway {{ $runway->designation }}</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.runways.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to runways</a>
        <div class="flex gap-2">
            <a href="{{ route('admin.runways.edit', $runway) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Edit</a>
            <form method="POST" action="{{ route('admin.runways.destroy', $runway) }}" onsubmit="return confirm('Delete this runway?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100">Delete</button>
            </form>
        </div>
    </div>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-gray-500">Designation</dt><dd class="mt-1 font-medium text-gray-800">{{ $runway->designation }}</dd></div>
            <div><dt class="text-gray-500">Status</dt><dd class="mt-1"><x-status-badge :status="$runway->status" /></dd></div>
            <div><dt class="text-gray-500">Airport</dt><dd class="mt-1 font-medium text-gray-800">{{ $runway->airport?->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Surface</dt><dd class="mt-1 font-medium capitalize text-gray-800">{{ $runway->surface }}</dd></div>
            <div><dt class="text-gray-500">Length</dt><dd class="mt-1 font-medium text-gray-800">{{ number_format($runway->length_m) }} m</dd></div>
            <div><dt class="text-gray-500">Width</dt><dd class="mt-1 font-medium text-gray-800">{{ $runway->width_m !== null ? number_format($runway->width_m).' m' : '—' }}</dd></div>
        </dl>
    </div>
</x-admin-layout>
