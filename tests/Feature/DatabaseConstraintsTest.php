<?php

namespace Tests\Feature;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Models\Passenger;
use App\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseConstraintsTest extends TestCase
{
    use RefreshDatabase;

    private Airline $airline;

    private Aircraft $aircraft;

    private FlightSchedule $schedule;

    private Passenger $passenger;

    protected function setUp(): void
    {
        parent::setUp();

        $airline = Airline::create([
            'name' => 'Test Air', 'iata_code' => 'TA', 'icao_code' => 'TST', 'country' => 'India',
        ]);

        $aircraft = Aircraft::create([
            'airline_id' => $airline->id, 'registration_number' => 'VT-TST',
            'manufacturer' => 'Airbus', 'model' => 'A320', 'seat_capacity' => 180,
        ]);

        $delhi = Airport::create([
            'name' => 'Delhi', 'iata_code' => 'DEL', 'icao_code' => 'VIDP',
            'city' => 'Delhi', 'country' => 'India', 'timezone' => 'Asia/Kolkata',
        ]);

        $mumbai = Airport::create([
            'name' => 'Mumbai', 'iata_code' => 'BOM', 'icao_code' => 'VABB',
            'city' => 'Mumbai', 'country' => 'India', 'timezone' => 'Asia/Kolkata',
        ]);

        $flight = Flight::create([
            'airline_id' => $airline->id, 'flight_number' => 'TA-100', 'flight_type' => 'domestic',
        ]);

        $this->schedule = FlightSchedule::create([
            'flight_id' => $flight->id,
            'aircraft_id' => $aircraft->id,
            'departure_airport_id' => $delhi->id,
            'arrival_airport_id' => $mumbai->id,
            'scheduled_departure' => '2026-11-01 10:00',
            'scheduled_arrival' => '2026-11-01 12:00',
            'base_fare' => 5000,
            'status' => 'scheduled',
        ]);

        $this->passenger = Passenger::create([
            'first_name' => 'Test', 'last_name' => 'Passenger', 'email' => 'test.passenger@example.com',
        ]);

        $this->airline = $airline;
        $this->aircraft = $aircraft;
    }

    private function makeBooking(string $reference, string $seat): Booking
    {
        return Booking::create([
            'booking_reference' => $reference,
            'passenger_id' => $this->passenger->id,
            'flight_schedule_id' => $this->schedule->id,
            'travel_class' => 'economy',
            'seat_number' => $seat,
            'status' => 'confirmed',
            'total_amount' => 5000,
            'booked_at' => '2026-10-20 10:00',
        ]);
    }

    public function test_same_seat_cannot_be_sold_twice_on_one_schedule(): void
    {
        $this->makeBooking('AAA111', '12A');

        $this->expectException(QueryException::class);

        $this->makeBooking('BBB222', '12A');
    }

    public function test_seat_is_unique_per_schedule_not_globally(): void
    {
        $this->makeBooking('AAA111', '12A');

        $secondPassenger = Passenger::create([
            'first_name' => 'Other', 'last_name' => 'Passenger', 'email' => 'other@example.com',
        ]);

        $otherAirline = Airline::create([
            'name' => 'Second Air', 'iata_code' => 'SB', 'icao_code' => 'SBA', 'country' => 'India',
        ]);
        $otherAircraft = Aircraft::create([
            'airline_id' => $otherAirline->id, 'registration_number' => 'VT-OTH',
            'manufacturer' => 'Boeing', 'model' => '737', 'seat_capacity' => 180,
        ]);
        $otherFlight = Flight::create([
            'airline_id' => $otherAirline->id, 'flight_number' => 'SB-100', 'flight_type' => 'domestic',
        ]);
        $otherSchedule = FlightSchedule::create([
            'flight_id' => $otherFlight->id,
            'aircraft_id' => $otherAircraft->id,
            'departure_airport_id' => $this->schedule->departure_airport_id,
            'arrival_airport_id' => $this->schedule->arrival_airport_id,
            'scheduled_departure' => '2026-11-01 14:00',
            'scheduled_arrival' => '2026-11-01 16:00',
            'base_fare' => 5000,
            'status' => 'scheduled',
        ]);

        $booking = Booking::create([
            'booking_reference' => 'CCC333',
            'passenger_id' => $secondPassenger->id,
            'flight_schedule_id' => $otherSchedule->id,
            'travel_class' => 'economy',
            'seat_number' => '12A',
            'status' => 'confirmed',
            'total_amount' => 5000,
            'booked_at' => '2026-10-20 10:00',
        ]);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'seat_number' => '12A']);
    }

    public function test_duplicate_ticket_number_is_rejected(): void
    {
        $first = $this->makeBooking('AAA111', '12A');
        $second = $this->makeBooking('BBB222', '12B');

        Ticket::create([
            'booking_id' => $first->id, 'ticket_number' => 'TKT-DUPE', 'fare' => 5000,
            'status' => 'issued', 'issued_at' => '2026-10-20 10:05',
        ]);

        $this->expectException(QueryException::class);

        Ticket::create([
            'booking_id' => $second->id, 'ticket_number' => 'TKT-DUPE', 'fare' => 5000,
            'status' => 'issued', 'issued_at' => '2026-10-20 10:05',
        ]);
    }

    public function test_airline_with_aircraft_cannot_be_deleted(): void
    {
        $this->expectException(QueryException::class);

        $this->airline->delete();
    }
}
