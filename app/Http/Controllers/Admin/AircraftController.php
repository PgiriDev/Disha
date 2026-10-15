<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAircraftRequest;
use App\Http\Requests\Admin\UpdateAircraftRequest;
use App\Models\Aircraft;
use App\Models\Airline;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AircraftController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Aircraft::class);

        $aircraft = Aircraft::query()
            ->with('airline')
            ->withCount('flightSchedules')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->value();
                $query->where(function ($query) use ($search) {
                    $query->where('registration_number', 'like', "%{$search}%")
                        ->orWhere('manufacturer', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('airline'), function ($query) use ($request) {
                $query->where('airline_id', $request->integer('airline'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->value());
            })
            ->orderBy('registration_number')
            ->paginate(10)
            ->withQueryString();

        $airlines = Airline::orderBy('name')->get();

        return view('admin.aircraft.index', compact('aircraft', 'airlines'));
    }

    public function create(): View
    {
        $this->authorize('create', Aircraft::class);

        $airlines = Airline::orderBy('name')->get();

        return view('admin.aircraft.create', compact('airlines'));
    }

    public function store(StoreAircraftRequest $request): RedirectResponse
    {
        $this->authorize('create', Aircraft::class);

        $aircraft = DB::transaction(fn () => Aircraft::create($request->validated()));

        return redirect()
            ->route('admin.aircraft.show', $aircraft)
            ->with('success', "Aircraft \"{$aircraft->registration_number}\" was created successfully.");
    }

    public function show(Aircraft $aircraft): View
    {
        $this->authorize('view', $aircraft);

        $aircraft->load('airline')->loadCount('flightSchedules');

        return view('admin.aircraft.show', compact('aircraft'));
    }

    public function edit(Aircraft $aircraft): View
    {
        $this->authorize('update', $aircraft);

        $airlines = Airline::orderBy('name')->get();

        return view('admin.aircraft.edit', compact('aircraft', 'airlines'));
    }

    public function update(UpdateAircraftRequest $request, Aircraft $aircraft): RedirectResponse
    {
        $this->authorize('update', $aircraft);

        DB::transaction(fn () => $aircraft->update($request->validated()));

        return redirect()
            ->route('admin.aircraft.show', $aircraft)
            ->with('success', "Aircraft \"{$aircraft->registration_number}\" was updated successfully.");
    }

    public function destroy(Aircraft $aircraft): RedirectResponse
    {
        $this->authorize('delete', $aircraft);

        if ($aircraft->flightSchedules()->exists()) {
            return back()->with('error', 'This aircraft cannot be deleted because it is still assigned to flight schedules.');
        }

        try {
            DB::transaction(fn () => $aircraft->delete());
        } catch (QueryException $exception) {
            return back()->with('error', 'This aircraft cannot be deleted because related records still reference it.');
        }

        return redirect()
            ->route('admin.aircraft.index')
            ->with('success', "Aircraft \"{$aircraft->registration_number}\" was deleted successfully.");
    }
}
