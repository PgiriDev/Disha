<?php

namespace Tests\Feature\Admin;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Gate;
use App\Models\Runway;
use App\Models\Terminal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AdminViewsRenderTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_create_screens_render_for_all_resources(): void
    {
        $admin = $this->admin();

        foreach (['airlines', 'airports', 'aircraft', 'terminals', 'gates', 'runways'] as $resource) {
            $this->actingAs($admin)->get("/admin/{$resource}/create")->assertOk();
        }
    }

    public function test_show_and_edit_screens_render_for_all_resources(): void
    {
        $admin = $this->admin();

        $airline = Airline::create(['name' => 'Lanka Airways', 'iata_code' => 'LA', 'icao_code' => 'LAW', 'country' => 'LK']);
        $airport = Airport::create(['name' => 'BIA', 'iata_code' => 'CMB', 'icao_code' => 'VCBI', 'city' => 'Colombo', 'country' => 'LK', 'timezone' => 'Asia/Colombo']);
        $aircraft = Aircraft::create(['airline_id' => $airline->id, 'registration_number' => '4R-ABC', 'manufacturer' => 'Airbus', 'model' => 'A320', 'seat_capacity' => 180, 'status' => 'active']);
        $terminal = Terminal::create(['airport_id' => $airport->id, 'name' => 'T1', 'code' => 'T1']);
        $gate = Gate::create(['terminal_id' => $terminal->id, 'code' => 'A1', 'status' => 'available']);
        $runway = Runway::create(['airport_id' => $airport->id, 'designation' => '04/22', 'length_m' => 3300, 'surface' => 'asphalt', 'status' => 'active']);

        $resources = [
            'airlines' => $airline,
            'airports' => $airport,
            'aircraft' => $aircraft,
            'terminals' => $terminal,
            'gates' => $gate,
            'runways' => $runway,
        ];

        foreach ($resources as $resource => $model) {
            $this->actingAs($admin)->get("/admin/{$resource}/{$model->id}")->assertOk();
            $this->actingAs($admin)->get("/admin/{$resource}/{$model->id}/edit")->assertOk();
        }
    }
}
