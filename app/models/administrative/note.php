<?php
namespace App\models\administrative;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class note extends Model
{
    protected $fillable = ['note', 'importance_level', 'created_by', 'target_type', 'target_id', 'context_type', 'context_id', 'action_required', 'is_pinned', 'due_date', 'read_at', 'status', 'patient_id', 'reservation_id', 'visit_id'];

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    public function context(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function reads()
    {
        return $this->hasMany(NoteRead::class);
    }

    public function hasBeenReadBy($user_id)
    {
        return $this->reads()->where('user_id', $user_id)->exists();
    }

    public function getReadTimeFor($user_id)
    {
        return $this->reads()->where('user_id', $user_id)->value('read_at');
    }
}