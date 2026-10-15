<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRunwayRequest;
use App\Http\Requests\Admin\UpdateRunwayRequest;
use App\Models\Airport;
use App\Models\Runway;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RunwayController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Runway::class);

        $runways = Runway::query()
            ->with('airport')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->value();
                $query->where('designation', 'like', "%{$search}%");
            })
            ->when($request->filled('airport'), function ($query) use ($request) {
                $query->where('airport_id', $request->integer('airport'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->value());
            })
            ->orderBy('airport_id')
            ->orderBy('designation')
            ->paginate(10)
            ->withQueryString();

        $airports = Airport::orderBy('name')->get();

        return view('admin.runways.index', compact('runways', 'airports'));
    }

    public function create(): View
    {
        $this->authorize('create', Runway::class);

        $airports = Airport::orderBy('name')->get();

        return view('admin.runways.create', compact('airports'));
    }

    public function store(StoreRunwayRequest $request): RedirectResponse
    {
        $this->authorize('create', Runway::class);

        $runway = DB::transaction(fn () => Runway::create($request->validated()));

        return redirect()
            ->route('admin.runways.show', $runway)
            ->with('success', "Runway \"{$runway->designation}\" was created successfully.");
    }

    public function show(Runway $runway): View
    {
        $this->authorize('view', $runway);

        $runway->load('airport');

        return view('admin.runways.show', compact('runway'));
    }

    public function edit(Runway $runway): View
    {
        $this->authorize('update', $runway);

        $airports = Airport::orderBy('name')->get();

        return view('admin.runways.edit', compact('runway', 'airports'));
    }

    public function update(UpdateRunwayRequest $request, Runway $runway): RedirectResponse
    {
        $this->authorize('update', $runway);

        DB::transaction(fn () => $runway->update($request->validated()));

        return redirect()
            ->route('admin.runways.show', $runway)
            ->with('success', "Runway \"{$runway->designation}\" was updated successfully.");
    }

    public function destroy(Runway $runway): RedirectResponse
    {
        $this->authorize('delete', $runway);

        try {
            DB::transaction(fn () => $runway->delete());
        } catch (QueryException $exception) {
            return back()->with('error', 'This runway cannot be deleted because related records still reference it.');
        }

        return redirect()
            ->route('admin.runways.index')
            ->with('success', "Runway \"{$runway->designation}\" was deleted successfully.");
    }
}
