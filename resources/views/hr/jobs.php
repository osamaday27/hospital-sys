<?php

namespace App\models\hr;
use App\models\hr\section;
use App\models\hr\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class jobs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'section_id',
        'status',
    ];
    protected $table = 'hr_jobs';

    public function sections()
    {
        return $this->belongsTo(section::class, 'section_id');
    }

    // public function employees()
    // {
    //     return $this->belongsTo(Employee::class, 'manager_id');
    // }
}
