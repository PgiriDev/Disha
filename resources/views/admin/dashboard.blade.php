<x-admin-layout>
    <x-slot name="header">Dashboard</x-slot>

    @php
        $cards = [
            ['label' => 'Airports', 'value' => $stats['airports'], 'route' => 'admin.airports.index', 'accent' => 'text-sky-600'],
            ['label' => 'Airlines', 'value' => $stats['airlines'], 'route' => 'admin.airlines.index', 'accent' => 'text-indigo-600'],
            ['label' => 'Aircraft', 'value' => $stats['aircraft'], 'route' => 'admin.aircraft.index', 'accent' => 'text-emerald-600'],
            ['label' => 'Terminals', 'value' => $stats['terminals'], 'route' => 'admin.terminals.index', 'accent' => 'text-amber-600'],
            ['label' => 'Gates', 'value' => $stats['gates'], 'route' => 'admin.gates.index', 'accent' => 'text-rose-600'],
            ['label' => 'Runways', 'value' => $stats['runways'], 'route' => 'admin.runways.index', 'accent' => 'text-cyan-600'],
            ['label' => 'Flights', 'value' => $stats['flights'], 'route' => null, 'accent' => 'text-violet-600'],
            ['label' => 'Scheduled Flights', 'value' => $stats['flight_schedules'], 'route' => null, 'accent' => 'text-slate-600'],
        ];
    @endphp

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        @foreach ($cards as $card)
            @if ($card['route'])
                <a href="{{ route($card['route']) }}"
                   class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            @else
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            @endif
                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $card['label'] }}</div>
                <div class="mt-2 text-3xl font-semibold {{ $card['accent'] }}">{{ number_format($card['value']) }}</div>
            @if ($card['route'])
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>

    <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-gray-800">Recent Flight Schedules</h2>
            <span class="text-xs text-gray-400">Latest {{ $recentSchedules->count() }}</span>
        </div>

        @if ($recentSchedules->isEmpty())
            <div class="px-5 py-12 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                <p class="mt-3 text-sm font-medium text-gray-600">No flight schedules yet</p>
                <p class="mt-1 text-xs text-gray-400">Schedules will appear here once they are created.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Flight</th>
                            <th class="px-5 py-3">Route</th>
                            <th class="px-5 py-3">Departure</th>
                            <th class="px-5 py-3">Aircraft</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($recentSchedules as $schedule)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-5 py-3 font-medium text-gray-800">
                                    {{ $schedule->flight?->airline?->iata_code }}{{ $schedule->flight?->flight_number ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3 text-gray-600">
                                    {{ $schedule->departureAirport?->iata_code ?? '—' }}
                                    <span class="mx-1 text-gray-400">&rarr;</span>
                                    {{ $schedule->arrivalAirport?->iata_code ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3 text-gray-600">
                                    {{ $schedule->scheduled_departure?->format('d M Y, H:i') ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3 text-gray-600">
                                    {{ $schedule->aircraft?->registration_number ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3">
                                    <x-status-badge :status="$schedule->status" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-admin-layout>
