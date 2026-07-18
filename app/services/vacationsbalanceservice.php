<?php

namespace App\services;

use App\models\hr\Employee;
use App\models\hr\hr_vacations;
use App\models\hr\hr_vacation_types;
use Illuminate\Support\Facades\DB;

class vacationsbalanceservice
{
    public function updateEmployeeBalance(hr_vacations $vacation)
    {
        DB::transaction(function () use ($vacation) {
            // Get the allocation record
            $allocation = DB::table('hr_allocations')
                ->join('employee_hr_allocation', 'hr_allocations.id', '=', 'employee_hr_allocation.hr_allocation_id')
                ->where('employee_hr_allocation.employee_id', $vacation->employee_id)
                ->where('hr_allocations.vacation_type', $vacation->vacation_type_id)
                ->first();

            if (!$allocation) {
                throw new \Exception("No allocation found for this employee and vacation type");
            }

            // Calculate new remaining balance
            $newBalance = $allocation->amount - $vacation->days_taken;

            if ($newBalance < 0) {
                throw new \Exception("Insufficient vacation balance");
            }

            // Update the allocation
            DB::table('employee_hr_allocation')
                ->where('employee_id', $vacation->employee_id)
                ->where('hr_allocation_id', $allocation->id)
                ->update(['remaining_balance' => $newBalance]);
        });
    }
}