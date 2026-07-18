<?php
namespace App\Events\Assets;

use App\models\assets\DisposalRequest;
use Illuminate\Foundation\Events\Dispatchable;

class DisposalRequestApproved
{
    use Dispatchable;

    public function __construct(public DisposalRequest $request) {}
}
