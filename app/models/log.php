<?php
namespace App\models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = ['action_type', 'table_name', 'record_id', 'field_name', 'old_value', 'new_value', 'performed_by', 'performed_at', 'method', 'full_url', 'ip_address', 'mac_address', 'user_agent'];

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}