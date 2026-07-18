<?php



namespace App\Models\supply_chain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class orderclosereasonsmodel extends Model
{
    use SoftDeletes;
    protected $table = 'order_close_reasons';

    protected $fillable = [
        'name_ar',
        'name_en',
        'status',
    ];
}
