<?php 

namespace App\Http\Controllers\general;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSettings_type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\log\LogController;
header('Content-Type: application/json;charset=utf-8');  
class Settings_typeController extends Controller 
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $settings_type=Settings_type::all();
    return view('pages.general.settings_type',compact('settings_type'));

  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(StoreSettings_type $request)
  {
     
    if(settings_type::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
    {
        return  redirect()->back()->withErrors([trans('setting_type_trans.existes') ]);
    }
    try
    {
        $validated = $request->validated();
        $Settings_type=new settings_type();
        $Settings_type->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $Settings_type->description=$request->description;
        $Settings_type->user_add=(Auth::user()->id);
        $Settings_type->save();
        $LogController = new LogController;
        $LogController->store('1','Settings type','اضافة نوع جديد','Add');
        Alert::success( '', trans('setting_type_trans.savesuccess'));
        return redirect()->route('settings_type.index');

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
    
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(StoreSettings_type $request)
  {
    try
    {
        $validated = $request->validated();
        $Settings_type= settings_type::find($request->id);
        $event=
              [
              
                'id'=>$request->id,
                'name_ar_old'=>$Settings_type->getTranslation('name','ar'),
                'name_en_old'=>$Settings_type->getTranslation('name','en'),
                'description_old'=>$Settings_type->description,
                'name_ar_new'=>$request->name_ar,
                'name_en_new'=>$request->name_en,
                'description_new'=>$request->description,
               ];
        $myJSONevent=json_encode($event,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        $Settings_type= settings_type::findorFail($request->id);
        $Settings_type->update([
        $Settings_type->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
        $Settings_type->description=$request->description,
        $Settings_type->user_add=(Auth::user()->id),
    ]);
 
    $LogController = new LogController;
    $LogController->store('1','Settings type',$myJSONevent,'update');
    Alert::success( '', trans('setting_type_trans.savesuccess'));
    return redirect()->route('settings_type.index');

    }
    catch(\Exception $e)
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
      $LogController = new LogController;
      $event=
      [
      
        'id'=>$request->id,
       ];
      $myJSONevent=json_encode($event,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
      $settings_type= settings_type::findorFail($request->id)->delete();
      
      $LogController->store('1','Settings type',$myJSONevent,'delete');
      Alert::success( '', trans('setting_type_trans.savesuccess'));
      return redirect()->route('settings_type.index');
    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }
  
}

?>