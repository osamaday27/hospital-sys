<?php


namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class medical_data_settingsController extends Controller
{


  public function index()
  {
   
    return view('settings.medical_data.medical_data_settings');

  }

}

?>
