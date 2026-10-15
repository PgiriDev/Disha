<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTerminalRequest;
use App\Http\Requests\Admin\UpdateTerminalRequest;
use App\Models\Airport;
use App\Models\Terminal;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TerminalController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Terminal::class);

        $terminals = Terminal::query()
            ->with('airport')
            ->withCount('gates')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->value();
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('airport'), function ($query) use ($request) {
                $query->where('airport_id', $request->integer('airport'));
            })
            ->orderBy('airport_id')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $airports = Airport::orderBy('name')->get();

        return view('admin.terminals.index', compact('terminals', 'airports'));
    }

    public function create(): View
    {
        $this->authorize('create', Terminal::class);

        $airports = Airport::orderBy('name')->get();

        return view('admin.terminals.create', compact('airports'));
    }

    public function store(StoreTerminalRequest $request): RedirectResponse
    {
        $this->authorize('create', Terminal::class);

        $terminal = DB::transaction(fn () => Terminal::create($request->validated()));

        return redirect()
            ->route('admin.terminals.show', $terminal)
            ->with('success', "Terminal \"{$terminal->name}\" was created successfully.");
    }

    public function show(Terminal $terminal): View
    {
        $this->authorize('view', $terminal);

        $terminal->load('airport')->loadCount('gates');
        $gates = $terminal->gates()->orderBy('code')->get();

        return view('admin.terminals.show', compact('terminal', 'gates'));
    }

    public function edit(Terminal $terminal): View
    {
        $this->authorize('update', $terminal);

        $airports = Airport::orderBy('name')->get();

        return view('admin.terminals.edit', compact('terminal', 'airports'));
    }

    public function update(UpdateTerminalRequest $request, Terminal $terminal): RedirectResponse
    {
        $this->authorize('update', $terminal);

        DB::transaction(fn () => $terminal->update($request->validated()));

        return redirect()
            ->route('admin.terminals.show', $terminal)
            ->with('success', "Terminal \"{$terminal->name}\" was updated successfully.");
    }

    public function destroy(Terminal $terminal): RedirectResponse
    {
        $this->authorize('delete', $terminal);

        if ($terminal->gates()->exists()) {
            return back()->with('error', 'This terminal cannot be deleted because it still has gates assigned to it.');
        }

        try {
            DB::transaction(fn () => $terminal->delete());
        } catch (QueryException $exception) {
            return back()->with('error', 'This terminal cannot be deleted because related records still reference it.');
        }

        return redirect()
            ->route('admin.terminals.index')
            ->with('success', "Terminal \"{$terminal->name}\" was deleted successfully.");
    }
}
