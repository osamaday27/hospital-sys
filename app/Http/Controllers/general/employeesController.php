<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storeemployees;
use App\models\employees;
use App\models\specialization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\web\file_storage;
class employeesController extends Controller
{
   use file_storage;
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $employees=employees::where('account_id',(Auth::user()->id_account))->get();
    $specialty=specialization::where('account_id',(Auth::user()->id_account))->get();
    return view('settings.hr.employees.index',compact('employees','specialty'));

  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    $specialty=specialization::where('account_id',(Auth::user()->id_account))->get();
    return view('settings.clinic.clinic_add',compact('specialty'));

  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Storeemployees $request)
  {
    
    try
    {
        $img=$this->file_storage($request->img_employee,'img_employee');
        $id1=$this->file_storage($request->img_id1,'img_id1');
        $id2=$this->file_storage($request->img_id2,'img_id2');
        $employees=new employees();
        $employees->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $employees->email=$request->email;
        $employees->phone=$request->phone;
        $employees->date_of_birth=$request->date_of_birth;
        $employees->id_number=$request->id_number;
        $employees->job=$request->job;
        $employees->specialization=$request->specialty;
        $employees->salary=$request->salary;
        $employees->address=$request->address;
        $employees->note=$request->note;
        $employees->img=$img;
        $employees->id_number_img1= $id1;
        $employees->id_number_img2= $id2;
        $employees->default_branch=false;
        $employees->status='active';
        $employees->account_id=(Auth::user()->id_account);
        $employees->user_id=(Auth::user()->id);
        $employees->save();
        session()->flash('add');
        return redirect()->route('employees.index');
    }catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  
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
    $clinic_data=clinic_settings::find($id);
    return view('settings.clinic.clinic_edit',compact('clinic_data'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Storeemployees $request)
  {
        try
    {
        $employees=employees::find($request->id);
        $img=$this->file_storage($request->img_employee,'img_employee');
        $id1=$this->file_storage($request->img_id1,'img_id1');
        $id2=$this->file_storage($request->img_id2,'img_id2');
        if($request->img_employee){$img=$this->file_storage($request->img_employee,'img_employee');}else{$img=$employees->img;}
        if($request->img_id1){$id1=$this->file_storage($request->img_id1,'img_id1');}else{$id1=$employees->id_number_img1;}
        if($request->img_id2){$id2=$this->file_storage($request->img_id2,'img_id2');}else{$id2=$employees->id_number_img2;}
         $employees->update([
          $employees->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
          $employees->email=$request->email,
          $employees->phone=$request->phone,
          $employees->date_of_birth=$request->date_of_birth,
          $employees->id_number=$request->id_number,
          $employees->job=$request->job,
          $employees->specialization=$request->specialty,
          $employees->salary=$request->salary,
          $employees->address=$request->address,
          $employees->note=$request->note,
          $employees->img=$img,
          $employees->id_number_img1= $id1,
          $employees->id_number_img2= $id2,
          $employees->default_branch=false,
          $employees->status=$request->status,
    ]);
       
        session()->flash('add');
        return redirect()->route('employees.index');
    }catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  

  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy(Request $request)
  {
    try
    {

      $clinic_settings= clinic_settings::findorFail($request->id)->delete();
       session()->flash('edit_m');
        return redirect()->route('clinic_settings.index');

    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }

}

?>