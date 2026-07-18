<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class disposalapproval extends Model
{
    use SoftDeletes;

    protected $table = 'approvals';

    protected $fillable = [
  'context','request_id','level','role_or_committee',
  'assignee_user_id','assignee_employee_id',
  'approver_user_id','approver_employee_id',
  'decision','decision_notes','decided_at','created_by'
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function request(){ return $this->belongsTo(DisposalRequest::class, 'request_id'); }
    public function approverUser(){ return $this->belongsTo(\App\models\User::class, 'approver_user_id'); }
    public function approverEmployee(){ return $this->belongsTo(\App\models\hr\Employee::class, 'approver_employee_id'); }
        public function assigneeUser()
    {
        return $this->belongsTo(\App\models\User::class, 'assignee_user_id');
    }
}
