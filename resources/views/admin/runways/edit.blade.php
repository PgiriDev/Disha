<x-admin-layout>
    <x-slot name="header">Edit Runway</x-slot>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.runways.update', $runway) }}">
            @csrf
            @method('PUT')
            @include('admin.runways._form')

            <div class="mt-6 flex items-center gap-4">
                <x-primary-button>Save Changes</x-primary-button>
                <a href="{{ route('admin.runways.show', $runway) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
