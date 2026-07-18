<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;

// موديل المورد من مشروعك
use App\Models\Suppliers\suppliersdata as SuppliersData;

class assetdocument extends Model
{
    protected $table = 'asset_documents';

    protected $fillable = [
        'asset_id','doc_type','doc_no','doc_date',
        'supplier_id','cost','file_path','notes'
    ];

    protected $casts = [
        'doc_date' => 'date',
        'cost'     => 'decimal:2',
    ];

    public function asset(){ return $this->belongsTo(assetitem::class,'asset_id'); }
    public function supplier(){ return $this->belongsTo(SuppliersData::class,'supplier_id'); }
}
