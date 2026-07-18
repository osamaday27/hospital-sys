<?php
namespace App\models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientItemRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'store_id', 'request_date', 'reason',
        'status', 'request_number', 'created_by', 'updated_by', 'deleted_by',
    ];

    public function items()
    {
        return $this->hasMany(ClientItemRequestItem::class, 'request_id');
    }

    public function client()
    {
        return $this->belongsTo(\App\models\administrative\medical_record::class, 'client_id');
    }

    public function store()
    {
        return $this->belongsTo(\App\models\supply_chain\storemaindata::class, 'store_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\models\User::class, 'created_by');
    }
    public function user()
    {
        return $this->belongsTo(\App\models\User::class, 'created_by');
    }
    public function issuer()
    {
        return $this->belongsTo(\App\models\User::class, 'created_by');
    }
}