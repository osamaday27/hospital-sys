<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storescheduling;
use App\models\scheduling_settings;
use App\models\clinic_settings;
use App\models\employees;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class schedulingController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {

    $employees=employees::where('account_id',Auth::user()->id_account)->where('job','doctor')->get();
    return view('settings.scheduling.index',compact('employees'));

  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
         $clinic=clinic_settings::where('account_id',Auth::user()->id_account)->get();
        $doctor=employees::where('job','doctor')->get();
        return view('settings.scheduling.create',compact('doctor','clinic'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    

  function add_scheduling($day,$first_shift_from,$first_shift_to,$second_shift_from,$second_shift_to,$third_shift_from,$third_shift_to,$id_doctor,$id_clinic)
       {
        scheduling_settings::create([
                'day' => $day,
                'first_shift_from' => $first_shift_from,  
                'first_shift_to' =>  $first_shift_to,  
                'second_shift_from' =>$second_shift_from,  
                'second_shift_to' => $second_shift_to,  
                'third_shift_from' => $third_shift_from,  
                'third_shift_to' => $third_shift_to,  
                'account_id' => (Auth::user()->id_account),
                'status' => "new",
                'user_id' =>(Auth::user()->id),
                'id_doctor' => $id_doctor,
                'id_clinic' => $id_clinic,
            ]);

       } 
   $selectedCheckboxes = $request->input('day', []);

        foreach ($selectedCheckboxes as $checkbox)
         {
          if($checkbox=='saturday')
          {
             add_scheduling($checkbox,$request->first_shift_from_saturday,$request->first_shift_to_saturday,$request->second_shift_from_saturday,$request->second_shift_to_saturday,$request->third_shift_from_saturday,$request->third_shift_to_saturday,$request->id_doctor,$request->id_clinic);
          }
          if($checkbox=='sunday')
          {
             add_scheduling($checkbox,$request->first_shift_from_sunday,$request->first_shift_to_sunday,$request->second_shift_from_sunday,$request->second_shift_to_sunday,$request->third_shift_from_sunday,$request->third_shift_to_sunday,$request->id_doctor,$request->id_clinic);
          }
          if($checkbox=='monday')
          {
             add_scheduling($checkbox,$request->first_shift_from_monday,$request->first_shift_to_monday,$request->second_shift_from_monday,$request->second_shift_to_monday,$request->third_shift_from_monday,$request->third_shift_to_monday,$request->id_doctor,$request->id_clinic);
          }
          if($checkbox=='tuesday')
          {
             add_scheduling($checkbox,$request->first_shift_from_tuesday,$request->first_shift_to_tuesday,$request->second_shift_from_tuesday,$request->second_shift_to_tuesday,$request->third_shift_from_tuesday,$request->third_shift_to_tuesday,$request->id_doctor,$request->id_clinic);
          }
          if($checkbox=='wednesday')
          {
             add_scheduling($checkbox,$request->first_shift_from_wednesday,$request->first_shift_to_wednesday,$request->second_shift_from_wednesday,$request->second_shift_to_wednesday,$request->third_shift_from_wednesday,$request->third_shift_to_wednesday,$request->id_doctor,$request->id_clinic);
          }
          if($checkbox=='thursday')
          {
             add_scheduling($checkbox,$request->first_shift_from_thursday,$request->first_shift_to_thursday,$request->second_shift_from_thursday,$request->second_shift_to_thursday,$request->third_shift_from_thursday,$request->third_shift_to_thursday,$request->id_doctor,$request->id_clinic);
          }
          if($checkbox=='friday')
          {
             add_scheduling($checkbox,$request->first_shift_from_friday,$request->first_shift_to_friday,$request->second_shift_from_friday,$request->second_shift_to_friday,$request->third_shift_from_friday,$request->third_shift_to_friday,$request->id_doctor,$request->id_clinic);
          }
        }
        return  redirect()->back()->with('success', trans('message_trans.save'));

  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
   
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Storescheduling $request)
  {
       

  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy(Request $request)
  {
      
  }

}

?>