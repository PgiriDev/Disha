<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGateRequest;
use App\Http\Requests\Admin\UpdateGateRequest;
use App\Models\Airport;
use App\Models\Gate;
use App\Models\Terminal;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GateController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Gate::class);

        $gates = Gate::query()
            ->with('terminal.airport')
            ->withCount(['flightGateAssignments', 'checkIns'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->value();
                $query->where('code', 'like', "%{$search}%");
            })
            ->when($request->filled('terminal'), function ($query) use ($request) {
                $query->where('terminal_id', $request->integer('terminal'));
            })
            ->when($request->filled('airport'), function ($query) use ($request) {
                $query->whereHas('terminal', fn ($query) => $query->where('airport_id', $request->integer('airport')));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->value());
            })
            ->orderBy('terminal_id')
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        $terminals = Terminal::with('airport')->orderBy('name')->get();
        $airports = Airport::orderBy('name')->get();

        return view('admin.gates.index', compact('gates', 'terminals', 'airports'));
    }

    public function create(): View
    {
        $this->authorize('create', Gate::class);

        $terminals = Terminal::with('airport')->orderBy('name')->get();

        return view('admin.gates.create', compact('terminals'));
    }

    public function store(StoreGateRequest $request): RedirectResponse
    {
        $this->authorize('create', Gate::class);

        $gate = DB::transaction(fn () => Gate::create($request->validated()));

        return redirect()
            ->route('admin.gates.show', $gate)
            ->with('success', "Gate \"{$gate->code}\" was created successfully.");
    }

    public function show(Gate $gate): View
    {
        $this->authorize('view', $gate);

        $gate->load('terminal.airport')->loadCount(['flightGateAssignments', 'checkIns']);

        return view('admin.gates.show', compact('gate'));
    }

    public function edit(Gate $gate): View
    {
        $this->authorize('update', $gate);

        $terminals = Terminal::with('airport')->orderBy('name')->get();

        return view('admin.gates.edit', compact('gate', 'terminals'));
    }

    public function update(UpdateGateRequest $request, Gate $gate): RedirectResponse
    {
        $this->authorize('update', $gate);

        DB::transaction(fn () => $gate->update($request->validated()));

        return redirect()
            ->route('admin.gates.show', $gate)
            ->with('success', "Gate \"{$gate->code}\" was updated successfully.");
    }

    public function destroy(Gate $gate): RedirectResponse
    {
        $this->authorize('delete', $gate);

        if ($gate->flightGateAssignments()->exists() || $gate->checkIns()->exists()) {
            return back()->with('error', 'This gate cannot be deleted because it still has gate assignments or check-ins linked to it.');
        }

        try {
            DB::transaction(fn () => $gate->delete());
        } catch (QueryException $exception) {
            return back()->with('error', 'This gate cannot be deleted because related records still reference it.');
        }

        return redirect()
            ->route('admin.gates.index')
            ->with('success', "Gate \"{$gate->code}\" was deleted successfully.");
    }
}
