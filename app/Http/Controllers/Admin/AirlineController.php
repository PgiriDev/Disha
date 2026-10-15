<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAirlineRequest;
use App\Http\Requests\Admin\UpdateAirlineRequest;
use App\Models\Airline;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AirlineController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Airline::class);

        $airlines = Airline::query()
            ->withCount(['aircraft', 'flights'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->value();
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('iata_code', 'like', "%{$search}%")
                        ->orWhere('icao_code', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('status'));
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.airlines.index', compact('airlines'));
    }

    public function create(): View
    {
        $this->authorize('create', Airline::class);

        return view('admin.airlines.create');
    }

    public function store(StoreAirlineRequest $request): RedirectResponse
    {
        $this->authorize('create', Airline::class);

        $airline = DB::transaction(fn () => Airline::create($request->validated()));

        return redirect()
            ->route('admin.airlines.show', $airline)
            ->with('success', "Airline \"{$airline->name}\" was created successfully.");
    }

    public function show(Airline $airline): View
    {
        $this->authorize('view', $airline);

        $airline->loadCount(['aircraft', 'flights']);
        $aircraft = $airline->aircraft()->orderBy('registration_number')->paginate(10, ['*'], 'aircraft_page');

        return view('admin.airlines.show', compact('airline', 'aircraft'));
    }

    public function edit(Airline $airline): View
    {
        $this->authorize('update', $airline);

        return view('admin.airlines.edit', compact('airline'));
    }

    public function update(UpdateAirlineRequest $request, Airline $airline): RedirectResponse
    {
        $this->authorize('update', $airline);

        DB::transaction(fn () => $airline->update($request->validated()));

        return redirect()
            ->route('admin.airlines.show', $airline)
            ->with('success', "Airline \"{$airline->name}\" was updated successfully.");
    }

    public function destroy(Airline $airline): RedirectResponse
    {
        $this->authorize('delete', $airline);

        if ($airline->aircraft()->exists() || $airline->flights()->exists()) {
            return back()->with('error', 'This airline cannot be deleted because it still has aircraft or flights assigned to it.');
        }

        try {
            DB::transaction(fn () => $airline->delete());
        } catch (QueryException $exception) {
            return back()->with('error', 'This airline cannot be deleted because related records still reference it.');
        }

        return redirect()
            ->route('admin.airlines.index')
            ->with('success', "Airline \"{$airline->name}\" was deleted successfully.");
    }
}
