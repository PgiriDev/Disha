<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * The seeders run in dependency order: reference data first, then
     * schedules, then transactional passenger data and operations.
     */
    public function run(): void
    {
        // Application logins (administrator role is the default).
        User::query()->updateOrCreate(
            ['email' => 'admin@airport.test'],
            [
                'name' => 'Airport Administrator',
                'password' => bcrypt('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'manager@airport.test'],
            [
                'name' => 'Operations Manager',
                'password' => bcrypt('password'),
                'role' => User::ROLE_MANAGER,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'agent@airport.test'],
            [
                'name' => 'Booking Agent',
                'password' => bcrypt('password'),
                'role' => User::ROLE_AGENT,
                'email_verified_at' => now(),
            ]
        );

        // Module 1: masters
        $this->call([
            AirlineSeeder::class,
            AirportSeeder::class,
            AircraftSeeder::class,
            TerminalGateRunwaySeeder::class,
        ]);

        // Module 2: scheduling and gate assignments
        $this->call([
            FlightScheduleSeeder::class,
            GateAssignmentSeeder::class,
        ]);

        // Module 3: passengers, bookings, tickets
        $this->call([
            PassengerBookingSeeder::class,
        ]);

        // Module 4: staff and operations
        $this->call([
            OperationStaffSeeder::class,
        ]);
    }
}
