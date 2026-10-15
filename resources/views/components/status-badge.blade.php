@props(['status'])

@php
    $map = [
        'scheduled' => 'bg-blue-100 text-blue-800',
        'delayed' => 'bg-amber-100 text-amber-800',
        'departed' => 'bg-indigo-100 text-indigo-800',
        'arrived' => 'bg-emerald-100 text-emerald-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'active' => 'bg-emerald-100 text-emerald-800',
        'available' => 'bg-emerald-100 text-emerald-800',
        'occupied' => 'bg-amber-100 text-amber-800',
        'maintenance' => 'bg-amber-100 text-amber-800',
        'closed' => 'bg-gray-200 text-gray-700',
        'retired' => 'bg-gray-200 text-gray-700',
    ];

    $classes = $map[$status] ?? 'bg-gray-100 text-gray-700';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {$classes}"]) }}>
    {{ str_replace('_', ' ', $status) }}
</span>
