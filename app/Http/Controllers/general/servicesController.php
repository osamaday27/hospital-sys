<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storeservices;
use App\models\services_settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class servicesController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $services=services_settings::all();
    return view('settings.accounts.services.index',compact('services'));

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
  public function store(Storeservices $request)
  {
    

   
    if(services_settings::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
    {
        return  redirect()->back()->withErrors([trans('services_trans.existes') ]);
    }
    try
    {
        $validated = $request->validated();
        $services=new services_settings();
        $services->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $services->description=$request->description;
        $services->preparation=$request->preparation;
        $services->user_id=(Auth::user()->id);
        $services->account_id=(Auth::user()->id_account);
        $services->save();
        return redirect()->route('services_settings.index')->with('success', trans('message_trans.save'));

       
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
    $services=services_settings::find($id);
    return view('settings.accounts.services.edit',compact('services'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Storeservices $request)
  {
        try
        {
        $validated = $request->validated();
        $services_settings= services_settings::findorFail($request->id);
        $services_settings->update([
        $services_settings->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
        $services_settings->description=$request->description,
        $services_settings->preparation=$request->preparation,
        ]);
         return redirect()->route('services_settings.index')->with('success', trans('message_trans.updated'));


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

      $services_settings= services_settings::findorFail($request->id)->delete();
       return redirect()->route('services_settings.index')->with('success', trans('message_trans.delete'));

    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }

}

?>
