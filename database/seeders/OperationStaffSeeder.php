<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\FlightCancellation;
use App\Models\FlightDelay;
use App\Models\FlightOperation;
use App\Models\FlightSchedule;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Demonstration data for Module 4 (departments, staff, operations, delays, cancellations).
 */
class OperationStaffSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Airport Operations Control', 'code' => 'OPS'],
            ['name' => 'Security', 'code' => 'SEC'],
            ['name' => 'Customer Service', 'code' => 'CS'],
            ['name' => 'Baggage Handling', 'code' => 'BAG'],
        ];

        foreach ($departments as $department) {
            Department::query()->updateOrCreate(
                ['code' => $department['code']],
                $department + ['description' => $department['name'].' department']
            );
        }

        $staffPlan = [
            ['employee_code' => 'EMP001', 'first_name' => 'Meera', 'last_name' => 'Iyer', 'job_title' => 'Operations Manager', 'department' => 'OPS', 'email' => 'meera.iyer@airport.test'],
            ['employee_code' => 'EMP002', 'first_name' => 'Arjun', 'last_name' => 'Rao', 'job_title' => 'Security Officer', 'department' => 'SEC', 'email' => 'arjun.rao@airport.test'],
            ['employee_code' => 'EMP003', 'first_name' => 'Neha', 'last_name' => 'Gupta', 'job_title' => 'Customer Service Agent', 'department' => 'CS', 'email' => 'neha.gupta@airport.test'],
            ['employee_code' => 'EMP004', 'first_name' => 'Vikram', 'last_name' => 'Das', 'job_title' => 'Baggage Handler', 'department' => 'BAG', 'email' => 'vikram.das@airport.test'],
        ];

        foreach ($staffPlan as $member) {
            $department = Department::query()->where('code', $member['department'])->firstOrFail();

            Staff::query()->updateOrCreate(
                ['employee_code' => $member['employee_code']],
                [
                    'department_id' => $department->id,
                    'first_name' => $member['first_name'],
                    'last_name' => $member['last_name'],
                    'job_title' => $member['job_title'],
                    'email' => $member['email'],
                    'hire_date' => '2023-01-15',
                    'status' => 'active',
                ]
            );
        }

        $opsManager = Staff::query()->where('employee_code', 'EMP001')->firstOrFail();

        $arrived = FlightSchedule::query()
            ->whereHas('flight', fn ($query) => $query->where('flight_number', '6E-2101'))
            ->first();

        if ($arrived) {
            $arrived->update(['status' => 'arrived']);

            FlightOperation::query()->updateOrCreate(
                ['flight_schedule_id' => $arrived->id],
                [
                    'actual_departure' => Carbon::parse('2026-11-01 06:05'),
                    'actual_arrival' => Carbon::parse('2026-11-01 08:15'),
                    'recorded_by' => $opsManager->id,
                    'remarks' => 'Departed and arrived within acceptable variance.',
                ]
            );
        }

        $delayed = FlightSchedule::query()
            ->whereHas('flight', fn ($query) => $query->where('flight_number', 'UK-737'))
            ->first();

        if ($delayed) {
            $delayed->update(['status' => 'delayed']);

            FlightDelay::query()->updateOrCreate(
                ['flight_schedule_id' => $delayed->id, 'reported_at' => Carbon::parse('2026-11-01 12:00')],
                [
                    'delay_minutes' => 45,
                    'reason' => 'Late arrival of inbound aircraft',
                    'reported_by' => $opsManager->id,
                ]
            );
        }

        $cancelled = FlightSchedule::query()
            ->whereHas('flight', fn ($query) => $query->where('flight_number', 'AI-101'))
            ->first();

        if ($cancelled) {
            $cancelled->update(['status' => 'cancelled']);

            FlightCancellation::query()->updateOrCreate(
                ['flight_schedule_id' => $cancelled->id],
                [
                    'reason' => 'Adverse weather at destination',
                    'cancelled_by' => $opsManager->id,
                    'cancelled_at' => Carbon::parse('2026-11-01 10:30'),
                    'compensation' => 5000.00,
                ]
            );
        }
    }
}
