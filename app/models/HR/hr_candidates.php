<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\models\general\city;
use App\models\general\nationalities;
use App\models\hr\hr_offers;

class hr_candidates extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'requisition_id',
        'first_name',
        'second_name',
        'third_name',
        'fourth_name',
        'birth_date',
        'gender',
        'national_id',
        'email',
        'city',
        'nationality',
        'cv_file',
        'status',
    ];

    protected $table = 'hr_candidates';

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Get the requisition this candidate belongs to
     */
    public function requisition()
    {
        return $this->belongsTo(hr_requisitions::class, 'requisition_id');
    }

    /**
     * Get the candidate's full name
     */
    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name . ' ' . 
            $this->second_name . ' ' . 
            $this->third_name . ' ' . 
            $this->fourth_name
        );
    }

    /**
     * Get the candidate's age
     */
    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->diffInYears(now()) : null;
    }

    /**
     * Scope to get male candidates
     */
    public function scopeMale($query)
    {
        return $query->where('gender', 'male');
    }

    /**
     * Scope to get female candidates
     */
    public function scopeFemale($query)
    {
        return $query->where('gender', 'female');
    }

    /**
     * Get the city this candidate belongs to
     */
    public function getcity()
    {
        return $this->belongsTo(city::class, 'city');
    }

    /**
     * Get the nationality of this candidate
     */
    public function getnationality()
    {
        return $this->belongsTo(nationalities::class, 'nationality');
    }

    /**
     * Get offers for this candidate
     */
    public function offers()
    {
        return $this->hasMany(hr_offers::class, 'candidate_id');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badgeClasses = [
            'new' => 'secondary',
            'shortlisted' => 'info',
            'accept_hr_interview' => 'warning',
            'accept_technical_interview' => 'primary',
            'accepted' => 'success',
            'refused' => 'danger',
        ];

        return $badgeClasses[$this->status] ?? 'secondary';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $statusTexts = [
            'new' => 'hr.status_new',
            'shortlisted' => 'hr.status_shortlisted',
            'accept_hr_interview' => 'hr.status_accept_hr_interview',
            'accept_technical_interview' => 'hr.status_accept_technical_interview',
            'accepted' => 'hr.status_accepted',
            'refused' => 'hr.status_refused',
        ];

        return trans($statusTexts[$this->status] ?? 'hr.status_new');
    }

    /**
     * Check if candidate can be offered
     */
    public function canBeOffered()
    {
        return $this->status === 'accepted';
    }
}
