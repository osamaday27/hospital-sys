<?php


namespace App\Http\Controllers\general;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storegeneral;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\general;
header('Content-Type: application/json;charset=utf-8');
use App\Http\Controllers\log\LogController;

class generalcontroller extends Controller
{
  

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
   
    return view('settings.settings');

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
  public function store(Storegeneral $request)
  {
      if(general::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
      {
          return  redirect()->back()->withErrors([trans('general_trans.existes') ]);
      }
      try
      {
          $validated = $request->validated();
          $general=new general();
          $general->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
          $general->id_settings_type=$request->Id_settings_type;
          $general->user_add=(Auth::user()->id);
          $general->save();
          $LogController = new LogController;
          $LogController->store('1','Application settings','اضافة اعدادات جديدة','Add');
          Alert::success( '', trans('general_trans.savesuccess'));
          return redirect()->route('application_setting.index');

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
  public function update(Storegeneral $request)
  {
    try
    {
        $validated = $request->validated();
        $general= general::find($request->id);
        $event=
              [

                'id'=>$request->id,
                'name_ar_old'=>$general->getTranslation('name','ar'),
                'name_en_old'=>$general->getTranslation('name','en'),
                'Id_settings_type_old'=>$general->id_settings_type,
                'name_ar_new'=>$request->name_ar,
                'name_en_new'=>$request->name_en,
                'Id_settings_type_new'=>$request->Id_settings_type,
               ];
        $myJSONevent=json_encode($event,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        $general= general::findorFail($request->id);
        $general->update([
        $general->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
        $general->id_settings_type=$request->Id_settings_type,
        $general->user_add=(Auth::user()->id),
    ]);

    $LogController = new LogController;
    $LogController->store('1','Settings type',$myJSONevent,'update');
    Alert::success( '', trans('general_trans.savesuccess'));
    return redirect()->route('application_setting.index');

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
      $general= general::findorFail($request->id)->delete();

      $LogController->store('1','Application settings',$myJSONevent,'delete');
      Alert::success( '', trans('general_trans.savesuccess'));
       return redirect()->route('application_setting.index');
    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }


}

?>