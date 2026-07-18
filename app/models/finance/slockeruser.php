<?php

namespace App\models\finance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\User;
class slockeruser extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;
    protected $table = 'fin_s_locker_user';
    protected $fillable = ['fin_s_locker_id', 'user_id'];
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}