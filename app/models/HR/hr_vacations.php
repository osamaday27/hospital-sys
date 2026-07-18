<?php
namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\models\hr\Employee;
use App\models\hr\hr_vacation_types;

class hr_vacations extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'vacation_type_id',
        'take_with',
        'from',
        'to',
        'date',
        'from_time',
        'to_time',
        'days_taken',
        'reason',
        'approval_status',
    ];

    protected $casts = [
        'from' => 'datetime',
        'to' => 'datetime',
        'days_taken' => 'float',
        'date' => 'date',
        'from_time' => 'datetime:H:i',
        'to_time' => 'datetime:H:i',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function vacation_type()
    {
        return $this->belongsTo(hr_vacation_types::class);
    }

    public function approveByManager()
    {
        if ($this->vacationType->approval_type === 'manager') {
            $this->update(['approval_status' => 'approved']);
        } elseif ($this->vacationType->approval_type === 'hr_manager') {
            $this->update(['approval_status' => 'approved_by_manager']);
        }
    }

    public function approveByHR()
    {
        if ($this->vacationType->approval_type === 'hr_manager' && $this->approval_status === 'approved_by_manager') {
            $this->update(['approval_status' => 'approved']);
        }
    }

    public function refuse()
    {
        $this->update(['approval_status' => 'refused']);
    }
}