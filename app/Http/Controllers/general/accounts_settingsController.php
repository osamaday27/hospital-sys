<?php


namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class accounts_settingsController extends Controller
{


  public function index()
  {
    return view('settings.accounts.accounts_settings');
  }

}

?>
