<x-admin-layout>
    <x-slot name="header">New Terminal</x-slot>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.terminals.store') }}">
            @csrf
            @include('admin.terminals._form', ['terminal' => null])

            <div class="mt-6 flex items-center gap-4">
                <x-primary-button>Create Terminal</x-primary-button>
                <a href="{{ route('admin.terminals.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
