<?php
namespace App\models\finance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\models\finance\contract_second_level;
use App\models\finance\contracts_details_last_level;
class contract_last_level extends Model
{
    use HasFactory, TracksChanges, SoftDeletes,HasTranslations;

    protected $fillable = [
        'name',  'company_code', 'commercial_registration', 
        'address', 'notes', 'claim_due_period', 'send_period', 
        'second_level_id', 'contract_start_date', 'entity_type'
    ];
    protected $table = 'fin_contract_last_level';

    public $timestamps = true;
    public $translatable = ['name'];
    // Relationship with the Price List
    public function second_level()
    {
        return $this->belongsTo(contract_second_level::class, 'second_level_id');
    }
    public function details()
    {
        return $this->hasOne(contracts_details_last_level::class, 'last_level_id');
    }
      public function secondLevel()
    {
        return $this->belongsTo(contract_second_level::class, 'second_level_id');
    }
}