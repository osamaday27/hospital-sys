<?php
namespace App\models\assets;

use Illuminate\Database\Eloquent\Model;

class assetsetting extends Model
{
     protected $table = 'asset_settings';
    protected $fillable = ['code_prefix','qr_size_px','qr_margin','qr_disk','qr_path','enable_rfid'];
    public $timestamps = false;

    public static function firstRow(): self {
        return static::query()->first() ?? new static(attributes: [
            'code_prefix' => 'AST-',
            'qr_size_px'  => 256,
            'qr_margin'   => 1,
            'qr_disk'     => 'public',
            'qr_path'     => 'attachments/qr/assets',
            'enable_rfid' => 0,
        ]);
    }
}
