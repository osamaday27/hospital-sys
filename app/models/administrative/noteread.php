<?php
namespace App\models\administrative;

use App\User;
use App\models\administrative\note;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class noteread extends Model
{
    protected $fillable = [
        'note_id',
        'user_id',
        'read_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }
}