@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label' => 'Airlines', 'route' => 'admin.airlines.index', 'active' => 'admin.airlines.*', 'icon' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8'],
        ['label' => 'Airports', 'route' => 'admin.airports.index', 'active' => 'admin.airports.*', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        ['label' => 'Aircraft', 'route' => 'admin.aircraft.index', 'active' => 'admin.aircraft.*', 'icon' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8'],
        ['label' => 'Terminals', 'route' => 'admin.terminals.index', 'active' => 'admin.terminals.*', 'icon' => 'M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01'],
        ['label' => 'Gates', 'route' => 'admin.gates.index', 'active' => 'admin.gates.*', 'icon' => 'M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'Runways', 'route' => 'admin.runways.index', 'active' => 'admin.runways.*', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin' }} &middot; {{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            {{-- Mobile sidebar overlay --}}
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                x-cloak
                @click="sidebarOpen = false"
                class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
            ></div>

            {{-- Sidebar --}}
            <aside
                x-cloak
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-40 w-64 transform bg-slate-900 text-slate-200 transition-transform duration-200 ease-in-out lg:translate-x-0"
            >
                <div class="flex h-16 items-center gap-2 border-b border-slate-800 px-6">
                    <x-application-logo class="h-8 w-8 fill-current text-sky-400" />
                    <span class="text-base font-semibold text-white">Airport Ops</span>
                </div>

                <nav class="mt-4 space-y-1 px-3">
                    @foreach ($navigation as $item)
                        @php $isActive = request()->routeIs($item['active']); @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition {{ $isActive ? 'bg-sky-500/10 text-sky-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                            </svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </aside>

            {{-- Main column --}}
            <div class="lg:pl-64">
                <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6">
                    <button
                        type="button"
                        @click="sidebarOpen = true"
                        class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden"
                    >
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <h1 class="flex-1 truncate text-lg font-semibold text-gray-800">
                        {{ $header ?? '' }}
                    </h1>

                    <div class="flex items-center gap-4">
                        <div class="hidden text-right sm:block">
                            <div class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-xs capitalize text-gray-500">{{ auth()->user()->role }}</div>
                        </div>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center rounded-full bg-slate-800 p-1 text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-600 font-semibold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    @include('admin.partials.flash')

                    {{ $slot }}
                </main>
            </div>
        </div>

        <style>[x-cloak]{display:none !important;}</style>
    </body>
</html>
