<?php

namespace App\models\medical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\User;

class medical_notes_patient extends Model
{
    protected $fillable = ['id_reservation', 'id_patient', 'notes', 'id_visit', 'type_visit', 'user_id', 'date', 'status'];
    protected $table = 'medical_notes_patient';
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

}