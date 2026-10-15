<x-admin-layout>
    <x-slot name="header">New Gate</x-slot>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.gates.store') }}">
            @csrf
            @include('admin.gates._form', ['gate' => null])

            <div class="mt-6 flex items-center gap-4">
                <x-primary-button>Create Gate</x-primary-button>
                <a href="{{ route('admin.gates.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
