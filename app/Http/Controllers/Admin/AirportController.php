<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAirportRequest;
use App\Http\Requests\Admin\UpdateAirportRequest;
use App\Models\Airport;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AirportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Airport::class);

        $airports = Airport::query()
            ->withCount(['terminals', 'runways'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->value();
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhere('iata_code', 'like', "%{$search}%")
                        ->orWhere('icao_code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('status'));
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.airports.index', compact('airports'));
    }

    public function create(): View
    {
        $this->authorize('create', Airport::class);

        return view('admin.airports.create');
    }

    public function store(StoreAirportRequest $request): RedirectResponse
    {
        $this->authorize('create', Airport::class);

        $airport = DB::transaction(fn () => Airport::create($request->validated()));

        return redirect()
            ->route('admin.airports.show', $airport)
            ->with('success', "Airport \"{$airport->name}\" was created successfully.");
    }

    public function show(Airport $airport): View
    {
        $this->authorize('view', $airport);

        $airport->loadCount(['terminals', 'runways', 'departureSchedules', 'arrivalSchedules']);
        $terminals = $airport->terminals()->withCount('gates')->orderBy('name')->get();
        $runways = $airport->runways()->orderBy('designation')->get();

        return view('admin.airports.show', compact('airport', 'terminals', 'runways'));
    }

    public function edit(Airport $airport): View
    {
        $this->authorize('update', $airport);

        return view('admin.airports.edit', compact('airport'));
    }

    public function update(UpdateAirportRequest $request, Airport $airport): RedirectResponse
    {
        $this->authorize('update', $airport);

        DB::transaction(fn () => $airport->update($request->validated()));

        return redirect()
            ->route('admin.airports.show', $airport)
            ->with('success', "Airport \"{$airport->name}\" was updated successfully.");
    }

    public function destroy(Airport $airport): RedirectResponse
    {
        $this->authorize('delete', $airport);

        $hasDependents = $airport->terminals()->exists()
            || $airport->runways()->exists()
            || $airport->departments()->exists()
            || $airport->departureSchedules()->exists()
            || $airport->arrivalSchedules()->exists();

        if ($hasDependents) {
            return back()->with('error', 'This airport cannot be deleted because it still has terminals, runways, departments, or flight schedules linked to it.');
        }

        try {
            DB::transaction(fn () => $airport->delete());
        } catch (QueryException $exception) {
            return back()->with('error', 'This airport cannot be deleted because related records still reference it.');
        }

        return redirect()
            ->route('admin.airports.index')
            ->with('success', "Airport \"{$airport->name}\" was deleted successfully.");
    }
}
