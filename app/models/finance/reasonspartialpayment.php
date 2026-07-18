<?php

namespace App\models\finance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\User;
class reasonspartialpayment extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;
    protected $table = 'fin_s_reasonspartialpayment';
    protected $fillable = ['name_ar', 'name_en', 'status'];
    public $timestamps = true;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}