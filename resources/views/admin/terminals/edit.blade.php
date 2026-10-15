<x-admin-layout>
    <x-slot name="header">Edit Terminal</x-slot>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.terminals.update', $terminal) }}">
            @csrf
            @method('PUT')
            @include('admin.terminals._form')

            <div class="mt-6 flex items-center gap-4">
                <x-primary-button>Save Changes</x-primary-button>
                <a href="{{ route('admin.terminals.show', $terminal) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
