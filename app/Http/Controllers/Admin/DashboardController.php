<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Models\Gate;
use App\Models\Runway;
use App\Models\Terminal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Airport::class);

        $stats = [
            'airports' => Airport::count(),
            'airlines' => Airline::count(),
            'aircraft' => Aircraft::count(),
            'flights' => Flight::count(),
            'flight_schedules' => FlightSchedule::count(),
            'terminals' => Terminal::count(),
            'gates' => Gate::count(),
            'runways' => Runway::count(),
        ];

        $recentSchedules = FlightSchedule::with([
            'flight.airline',
            'aircraft',
            'departureAirport',
            'arrivalAirport',
        ])
            ->orderByDesc('scheduled_departure')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentSchedules'));
    }
}
