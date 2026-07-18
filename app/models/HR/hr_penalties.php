<?php
namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\hr\Employee;
use App\models\hr\hr_violations;




class hr_penalties extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'violation_id',
        'date',
        'reason',
        'penalty_type',
        'no_days',
        'amount',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function violation()
    {
        return $this->belongsTo(hr_violations::class);
    }

}