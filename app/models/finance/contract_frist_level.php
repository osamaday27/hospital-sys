<?php
namespace App\models\finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\models\finance\price_lists;
class contract_frist_level extends Model
{
    use HasFactory, TracksChanges, SoftDeletes,HasTranslations;

    protected $fillable = [
        'name',  'company_code', 'commercial_registration', 
        'address', 'notes', 'claim_due_period', 'send_period', 
        'price_list_id', 'contract_start_date', 'entity_type'
    ];
    protected $table = 'fin_contract_frist_level';

    public $timestamps = true;
    public $translatable = ['name'];
    // Relationship with the Price List
    public function priceList()
    {
        return $this->belongsTo(price_lists::class, 'price_list_id');
    }
}