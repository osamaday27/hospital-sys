<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\models\hr\jobs;

class hr_requisitions extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'requisition_name',
        'job_id',
        'number_of_candidates',
        'required_experience_years',
        'specifications',
        'salary',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $table = 'hr_requisitions';

    protected $casts = [
        'approved_at' => 'datetime',
        'birth_date' => 'date',
    ];

    /**
     * Get the job this requisition is for
     */
    public function job()
    {
        return $this->belongsTo(jobs::class, 'job_id');
    }

    /**
     * Get the user who created this requisition
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this requisition
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all candidates for this requisition
     */
    public function candidates()
    {
        return $this->hasMany(hr_candidates::class, 'requisition_id');
    }

    /**
     * Scope to get only pending requisitions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get only approved requisitions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Check if requisition is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if requisition is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if requisition is refused
     */
    public function isRefused()
    {
        return $this->status === 'refused';
    }

    /**
     * Check if requisition is completed (all positions filled)
     */
    public function isCompleted()
    {
        return $this->candidates()->where('status', 'accepted')->count() >= $this->number_of_candidates;
    }

    /**
     * Get accepted candidates count
     */
    public function getAcceptedCandidatesCountAttribute()
    {
        return $this->candidates()->where('status', 'accepted')->count();
    }

    /**
     * Get remaining positions
     */
    public function getRemainingPositionsAttribute()
    {
        return max(0, $this->number_of_candidates - $this->accepted_candidates_count);
    }
}
