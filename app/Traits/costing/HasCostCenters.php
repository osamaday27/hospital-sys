<?php

namespace App\Traits\Costing;

use App\Models\costing\costcenter;
use App\Models\costing\costcenterassignment;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasCostCenters
{
    // جميع روابط هذا الموديل مع مراكز التكلفة (Pivot Model)
    public function costCenterAssignments(): MorphMany
    {
        return $this->morphMany(costcenterassignment::class, 'assignable');
    }

    // مراكز التكلفة المرتبطة بهذا الكيان (department/section/clinic/room)
    public function costcenters(): MorphToMany
    {
        return $this->morphToMany(
            costcenter::class,
            'assignable',
            'cost_center_assignments',
            'assignable_id',
            'cost_center_id'
        )->withPivot(['status','effective_from','effective_to','notes','user_id','created_at','updated_at','deleted_at'])
         ->wherePivotNull('deleted_at');
    }

    // ربط مركز تكلفة
    public function attachCostCenter(int $cost_center_id, array $extra = []): costcenterassignment
    {
        return $this->costCenterAssignments()->create(array_merge([
            'cost_center_id' => $cost_center_id,
            'status' => 'active',
            'user_id' => auth()->id(),
        ], $extra));
    }

    // إلغاء الربط (Soft delete)
    public function detachCostCenter(int $cost_center_id): void
    {
        $this->costCenterAssignments()
            ->where('cost_center_id', $cost_center_id)
            ->delete();
    }

    // تفعيل/تعطيل رابطة معينة
    public function toggleCostCenterStatus(int $assignment_id): void
    {
        $assignment = $this->costCenterAssignments()->whereKey($assignment_id)->firstOrFail();
        $assignment->status = $assignment->status === 'active' ? 'inactive' : 'active';
        $assignment->save();
    }
}