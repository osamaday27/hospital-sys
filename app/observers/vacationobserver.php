<?php

namespace App\observers;

use App\models\hr\hr_vacations;
use App\services\vacationbalanceservice;

class vacationobserver
{
    public function created(hr_vacations $vacation)
    {
        if ($vacation->vacation_type->has_allocation && $vacation->approval_status === 'approved') {
            (new VacationBalanceService())->updateEmployeeBalance($vacation);
        }
    }

    public function updated(hr_vacations $vacation)
    {
        // Handle balance updates if approval status changes
        if ($vacation->isDirty('approval_status') && $vacation->approval_status === 'approved') {
            if ($vacation->vacation_type->has_allocation) {
                (new VacationBalanceService())->updateEmployeeBalance($vacation);
            }
        }
    }
}