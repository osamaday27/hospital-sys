<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class hr_salary_rules extends Model
{

    protected $table = 'hr_salary_rules';
    
    protected $fillable = [
        'name',
        'type',
        'rule_type',
        'added_automatically',
        'sequence',
        'is_active'
    ];

    protected $casts = [
        'added_automatically' => 'boolean',
        'is_active' => 'boolean'
    ];

    public static function getRuleTypes($type)
    {
        $deductionTypes = ['absence', 'lateness', 'penalties', 'tax', 'insurance', 'loans', 'manual'];
        $additionTypes = ['incentive', 'overtime', 'free', 'basic_salary', 'allowances', 'gross_salary', 'net_salary', 'manual'];

        return $type === 'deduction' ? $deductionTypes : $additionTypes;
    }

    public static function validateUniqueRuleType($type, $ruleType, $id = null)
    {
        $query = self::where('type', $type)
            ->where('rule_type', $ruleType);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        return !$query->exists();
    }
} 