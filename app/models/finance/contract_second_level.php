<?php
namespace App\models\finance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\models\finance\contract_frist_level;


class contract_second_level extends Model
{
    
    use HasFactory, TracksChanges, SoftDeletes,HasTranslations;
    protected $fillable = [
        'name',  'company_code', 'commercial_registration', 
        'address', 'notes', 'claim_due_period', 'send_period', 
        'frist_level_id', 'contract_start_date', 'entity_type'
    ];
    protected $table = 'fin_contract_second_level';

    public $timestamps = true;
    public $translatable = ['name'];
    // Relationship with the Price List
    public function frist_level()
    {
        return $this->belongsTo(contract_frist_level::class, 'frist_level_id');
    }
   public function firstLevel()
    {
        return $this->belongsTo(contract_frist_level::class, 'frist_level_id');
    }  
  
}