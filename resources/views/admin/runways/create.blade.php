<x-admin-layout>
    <x-slot name="header">New Runway</x-slot>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.runways.store') }}">
            @csrf
            @include('admin.runways._form', ['runway' => null])

            <div class="mt-6 flex items-center gap-4">
                <x-primary-button>Create Runway</x-primary-button>
                <a href="{{ route('admin.runways.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
