<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\hr\Employee;
use Carbon\Carbon;

class hr_loans extends Model
{
    use SoftDeletes;

    protected $table = 'hr_loans';

    protected $fillable = [
        'employee_id',
        'loan_type',
        'amount',
        'loan_date',
        'no_installments',
        'installment_value',
        'remaining_installments',
        'notes',
        'status'
    ];

    protected $dates = [
        'loan_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'loan_date' => 'date:Y-m-d',
        'amount' => 'integer',
        'no_installments' => 'integer',
        'installment_value' => 'float',
        'remaining_installments' => 'integer'
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Accessors & Mutators
    public function setLoanTypeAttribute($value)
    {
        $this->attributes['loan_type'] = $value;
        // If loan type is instant, set installments to 1
        if ($value === 'instant') {
            $this->attributes['no_installments'] = 1;
        }
    }

    public function setNoInstallmentsAttribute($value)
    {
        $this->attributes['no_installments'] = $value;
        // Recalculate installment value whenever installments change
        if ($this->amount && $value) {
            $this->attributes['installment_value'] = $this->amount / $value;
        }
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value;
        // Recalculate installment value whenever amount changes
        if ($value && $this->no_installments) {
            $this->attributes['installment_value'] = $value / $this->no_installments;
        }
    }

    // Calculate remaining installments based on time passed
    public function calculateRemainingInstallments()
    {
        if ($this->loan_type === 'instant') {
            return 1;
        }

        $loanDate = Carbon::parse($this->loan_date);
        $monthsPassed = $loanDate->diffInMonths(now());
        
        return max(0, $this->no_installments - $monthsPassed);
    }

    // Boot method to handle automatic calculations
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Ensure installment value is calculated
            if (!$model->installment_value && $model->amount && $model->no_installments) {
                $model->installment_value = $model->amount / $model->no_installments;
            }

            // Calculate remaining installments before saving
            $model->remaining_installments = $model->calculateRemainingInstallments();
        });
    }
}