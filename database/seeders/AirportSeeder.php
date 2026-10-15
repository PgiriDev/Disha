<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

/**
 * Demonstration data for Module 1 (airports).
 */
class AirportSeeder extends Seeder
{
    public function run(): void
    {
        $airports = [
            [
                'name' => 'Indira Gandhi International Airport',
                'iata_code' => 'DEL',
                'icao_code' => 'VIDP',
                'city' => 'Delhi',
                'country' => 'India',
                'timezone' => 'Asia/Kolkata',
                'latitude' => 28.5562000,
                'longitude' => 77.1000000,
                'elevation_ft' => 777,
                'is_active' => true,
            ],
            [
                'name' => 'Chhatrapati Shivaji Maharaj International Airport',
                'iata_code' => 'BOM',
                'icao_code' => 'VABB',
                'city' => 'Mumbai',
                'country' => 'India',
                'timezone' => 'Asia/Kolkata',
                'latitude' => 19.0896000,
                'longitude' => 72.8656000,
                'elevation_ft' => 39,
                'is_active' => true,
            ],
            [
                'name' => 'Kempegowda International Airport',
                'iata_code' => 'BLR',
                'icao_code' => 'VOBL',
                'city' => 'Bengaluru',
                'country' => 'India',
                'timezone' => 'Asia/Kolkata',
                'latitude' => 13.1986000,
                'longitude' => 77.7066000,
                'elevation_ft' => 3000,
                'is_active' => true,
            ],
            [
                'name' => 'Chennai International Airport',
                'iata_code' => 'MAA',
                'icao_code' => 'VOMM',
                'city' => 'Chennai',
                'country' => 'India',
                'timezone' => 'Asia/Kolkata',
                'latitude' => 12.9941000,
                'longitude' => 80.1709000,
                'elevation_ft' => 52,
                'is_active' => true,
            ],
            [
                'name' => 'Netaji Subhas Chandra Bose International Airport',
                'iata_code' => 'CCU',
                'icao_code' => 'VECC',
                'city' => 'Kolkata',
                'country' => 'India',
                'timezone' => 'Asia/Kolkata',
                'latitude' => 22.6547000,
                'longitude' => 88.4467000,
                'elevation_ft' => 16,
                'is_active' => true,
            ],
        ];

        foreach ($airports as $airport) {
            Airport::query()->updateOrCreate(
                ['iata_code' => $airport['iata_code']],
                $airport
            );
        }
    }
}
