<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Models\Gate;
use App\Models\Runway;
use App\Models\Terminal;
use Illuminate\Database\Seeder;

/**
 * Demonstration data for Module 1 (terminals, gates, runways).
 */
class TerminalGateRunwaySeeder extends Seeder
{
    public function run(): void
    {
        $layout = [
            'DEL' => [
                'terminals' => [
                    'T1' => ['name' => 'Terminal 1', 'gates' => ['A1', 'A2', 'A3', 'A4']],
                    'T3' => ['name' => 'Terminal 3', 'gates' => ['31', '32', '33', '34', '35']],
                ],
                'runways' => [
                    ['designation' => '09/27', 'length_m' => 2813, 'width_m' => 45],
                    ['designation' => '10/28', 'length_m' => 3810, 'width_m' => 60],
                    ['designation' => '11/29', 'length_m' => 4430, 'width_m' => 60],
                ],
            ],
            'BOM' => [
                'terminals' => [
                    'T2' => ['name' => 'Terminal 2', 'gates' => ['21', '22', '23', '24']],
                ],
                'runways' => [
                    ['designation' => '09/27', 'length_m' => 3448, 'width_m' => 45],
                    ['designation' => '14/32', 'length_m' => 2871, 'width_m' => 45],
                ],
            ],
        ];

        foreach ($layout as $iataCode => $config) {
            $airport = Airport::query()->where('iata_code', $iataCode)->firstOrFail();

            foreach ($config['terminals'] as $code => $terminalData) {
                $terminal = Terminal::query()->updateOrCreate(
                    ['airport_id' => $airport->id, 'code' => $code],
                    ['name' => $terminalData['name'], 'passenger_capacity' => 5000]
                );

                foreach ($terminalData['gates'] as $gateCode) {
                    Gate::query()->updateOrCreate(
                        ['terminal_id' => $terminal->id, 'code' => $gateCode],
                        ['status' => 'available']
                    );
                }
            }

            foreach ($config['runways'] as $runway) {
                Runway::query()->updateOrCreate(
                    ['airport_id' => $airport->id, 'designation' => $runway['designation']],
                    $runway + ['surface' => 'asphalt', 'status' => 'active']
                );
            }
        }
    }
}
