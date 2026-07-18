<?php
namespace App\Events\Assets;

use App\models\assets\Disposal;
use Illuminate\Foundation\Events\Dispatchable;

class DisposalExecuted
{
    use Dispatchable;

    /** @var array|null $journal_blueprint هيكل قيد مقترح دون ترحيل */
    public function __construct(public Disposal $disposal, public ?array $journal_blueprint = null) {}
}
