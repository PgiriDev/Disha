<?php

namespace Database\Seeders;

use App\Models\Baggage;
use App\Models\Booking;
use App\Models\CheckIn;
use App\Models\FlightSchedule;
use App\Models\Passenger;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Demonstration data for Module 3 (passengers, bookings, tickets, check-ins, baggage).
 *
 * Seats are unique per flight schedule (enforced by a composite UNIQUE index),
 * and each booking has exactly one ticket (1:1).
 */
class PassengerBookingSeeder extends Seeder
{
    public function run(): void
    {
        $passengers = [
            ['first_name' => 'Aarav', 'last_name' => 'Sharma', 'email' => 'aarav.sharma@example.com', 'passport_number' => 'P1000001', 'nationality' => 'Indian', 'gender' => 'male', 'date_of_birth' => '1990-04-12'],
            ['first_name' => 'Diya', 'last_name' => 'Patel', 'email' => 'diya.patel@example.com', 'passport_number' => 'P1000002', 'nationality' => 'Indian', 'gender' => 'female', 'date_of_birth' => '1994-09-23'],
            ['first_name' => 'Rohan', 'last_name' => 'Verma', 'email' => 'rohan.verma@example.com', 'passport_number' => 'P1000003', 'nationality' => 'Indian', 'gender' => 'male', 'date_of_birth' => '1988-01-30'],
            ['first_name' => 'Isha', 'last_name' => 'Nair', 'email' => 'isha.nair@example.com', 'passport_number' => 'P1000004', 'nationality' => 'Indian', 'gender' => 'female', 'date_of_birth' => '1999-06-17'],
            ['first_name' => 'Kabir', 'last_name' => 'Singh', 'email' => 'kabir.singh@example.com', 'passport_number' => 'P1000005', 'nationality' => 'Indian', 'gender' => 'male', 'date_of_birth' => '1985-11-08'],
        ];

        foreach ($passengers as $passenger) {
            Passenger::query()->updateOrCreate(
                ['email' => $passenger['email']],
                $passenger
            );
        }

        // [passenger email, flight number, seat, travel class, booking reference, ticket number]
        $bookingPlan = [
            ['aarav.sharma@example.com', '6E-2101', '12A', 'economy', '6E1001', 'TKT6E1001'],
            ['diya.patel@example.com', '6E-2101', '12B', 'economy', '6E1002', 'TKT6E1002'],
            ['rohan.verma@example.com', '6E-2101', '12C', 'business', '6E1003', 'TKT6E1003'],
            ['isha.nair@example.com', 'UK-995', '6A', 'economy', 'UK1001', 'TKTUK1001'],
            ['kabir.singh@example.com', 'UK-995', '6B', 'economy', 'UK1002', 'TKTUK1002'],
        ];

        foreach ($bookingPlan as [$email, $flightNumber, $seat, $travelClass, $reference, $ticketNumber]) {
            $passenger = Passenger::query()->where('email', $email)->firstOrFail();

            $schedule = FlightSchedule::query()
                ->whereHas('flight', fn ($query) => $query->where('flight_number', $flightNumber))
                ->firstOrFail();

            $booking = Booking::query()->updateOrCreate(
                ['booking_reference' => $reference],
                [
                    'passenger_id' => $passenger->id,
                    'flight_schedule_id' => $schedule->id,
                    'travel_class' => $travelClass,
                    'seat_number' => $seat,
                    'status' => 'confirmed',
                    'total_amount' => $schedule->base_fare,
                    'booked_at' => Carbon::parse('2026-10-20 10:00'),
                ]
            );

            Ticket::query()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'ticket_number' => $ticketNumber,
                    'fare' => $booking->total_amount,
                    'status' => 'issued',
                    'issued_at' => Carbon::parse('2026-10-20 10:05'),
                ]
            );
        }

        // Check-in and baggage for the first two tickets (remaining tickets stay unchecked-in).
        $tickets = Ticket::query()->orderBy('id')->take(2)->get();

        foreach ($tickets as $index => $ticket) {
            CheckIn::query()->updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'boarding_pass_number' => 'BP'.str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                    'checked_in_at' => Carbon::parse('2026-11-01 04:30'),
                    'gate_id' => $ticket->booking->flightSchedule->gateAssignments()->value('gate_id'),
                    'status' => 'checked_in',
                ]
            );

            Baggage::query()->updateOrCreate(
                ['tag_number' => 'BAG'.str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT)],
                [
                    'ticket_id' => $ticket->id,
                    'weight_kg' => 14.50 + $index,
                    'type' => 'checked',
                    'status' => 'checked_in',
                ]
            );
        }
    }
}
