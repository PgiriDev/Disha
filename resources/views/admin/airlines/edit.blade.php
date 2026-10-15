<x-admin-layout>
    <x-slot name="header">Edit Airline</x-slot>

    <div class="max-w-3xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.airlines.update', $airline) }}">
            @csrf
            @method('PUT')
            @include('admin.airlines._form')

            <div class="mt-6 flex items-center gap-4">
                <x-primary-button>Save Changes</x-primary-button>
                <a href="{{ route('admin.airlines.show', $airline) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
