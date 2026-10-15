<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use App\Models\Airline;
use Illuminate\Database\Seeder;

/**
 * Demonstration data for Module 1 (aircraft fleet).
 */
class AircraftSeeder extends Seeder
{
    public function run(): void
    {
        $fleet = [
            '6E' => [
                ['registration_number' => 'VT-IND', 'manufacturer' => 'Airbus', 'model' => 'A320neo', 'seat_capacity' => 186, 'business_capacity' => 0, 'economy_capacity' => 186, 'status' => 'active'],
                ['registration_number' => 'VT-INA', 'manufacturer' => 'Airbus', 'model' => 'A320neo', 'seat_capacity' => 186, 'business_capacity' => 0, 'economy_capacity' => 186, 'status' => 'active'],
                ['registration_number' => 'VT-INB', 'manufacturer' => 'Airbus', 'model' => 'A321neo', 'seat_capacity' => 222, 'business_capacity' => 0, 'economy_capacity' => 222, 'status' => 'maintenance'],
            ],
            'AI' => [
                ['registration_number' => 'VT-AIA', 'manufacturer' => 'Boeing', 'model' => '737 MAX 8', 'seat_capacity' => 178, 'business_capacity' => 12, 'economy_capacity' => 166, 'status' => 'active'],
                ['registration_number' => 'VT-AIB', 'manufacturer' => 'Boeing', 'model' => '787-8 Dreamliner', 'seat_capacity' => 256, 'business_capacity' => 18, 'economy_capacity' => 238, 'status' => 'active'],
            ],
            'UK' => [
                ['registration_number' => 'VT-VTA', 'manufacturer' => 'Airbus', 'model' => 'A321neo', 'seat_capacity' => 188, 'business_capacity' => 12, 'economy_capacity' => 176, 'status' => 'active'],
                ['registration_number' => 'VT-VTB', 'manufacturer' => 'Airbus', 'model' => 'A320neo', 'seat_capacity' => 164, 'business_capacity' => 8, 'economy_capacity' => 156, 'status' => 'active'],
            ],
        ];

        foreach ($fleet as $iataCode => $aircraftList) {
            $airline = Airline::query()->where('iata_code', $iataCode)->firstOrFail();

            foreach ($aircraftList as $aircraft) {
                Aircraft::query()->updateOrCreate(
                    ['registration_number' => $aircraft['registration_number']],
                    $aircraft + ['airline_id' => $airline->id]
                );
            }
        }
    }
}
