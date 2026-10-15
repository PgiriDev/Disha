<?php

namespace Database\Seeders;

use App\Models\Airline;
use Illuminate\Database\Seeder;

/**
 * Demonstration data for Module 1 (airlines).
 */
class AirlineSeeder extends Seeder
{
    public function run(): void
    {
        $airlines = [
            [
                'name' => 'IndiGo',
                'iata_code' => '6E',
                'icao_code' => 'IGO',
                'country' => 'India',
                'callsign' => 'IFLY',
                'is_active' => true,
            ],
            [
                'name' => 'Air India',
                'iata_code' => 'AI',
                'icao_code' => 'AIC',
                'country' => 'India',
                'callsign' => 'AIRINDIA',
                'is_active' => true,
            ],
            [
                'name' => 'Vistara',
                'iata_code' => 'UK',
                'icao_code' => 'VTI',
                'country' => 'India',
                'callsign' => 'VISTARA',
                'is_active' => true,
            ],
        ];

        foreach ($airlines as $airline) {
            Airline::query()->updateOrCreate(
                ['iata_code' => $airline['iata_code']],
                $airline
            );
        }
    }
}
