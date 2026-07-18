<?php

namespace App\models\hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\models\hr\jobs;
use App\models\hr\hr_candidates;

class hr_offers extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'candidate_id',
        'offer_text',
        'job_id',
        'salary',
        'status',
        'created_by',
    ];

    protected $table = 'hr_offers';

    /**
     * Get the candidate this offer is for
     */
    public function candidate()
    {
        return $this->belongsTo(hr_candidates::class, 'candidate_id');
    }

    /**
     * Get the job this offer is for
     */
    public function job()
    {
        return $this->belongsTo(jobs::class, 'job_id');
    }

    /**
     * Get the user who created this offer
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get only new offers
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope to get only approved offers
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Check if offer is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if offer is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
