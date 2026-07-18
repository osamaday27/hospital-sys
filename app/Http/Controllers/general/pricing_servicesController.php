<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storepricing_services;
use App\models\pricing_settings;
use App\models\services_settings;
use App\models\pricing_services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class pricing_servicesController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $pricing_services=pricing_services::all();
    return view('settings.accounts.pricing_services.index',compact('pricing_services'));

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
  public function store(Storepricing_services $request)
  {
    

   
    if(pricing_services::where('id_pricing',$request->id_pricing)->where('id_services',$request->id_services)->exists())
    {
        return  redirect()->back()->with('info', trans('message_trans.exists'));
    }
    try
    {
        $validated = $request->validated();
        $pricing_services=new pricing_services();
        $pricing_services->id_pricing=$request->id_pricing;
        $pricing_services->id_services=$request->id_services;
        $pricing_services->cost_price=$request->cost_price;
        $pricing_services->patient_price=$request->patient_price;
        $pricing_services->user_id=(Auth::user()->id);
        $pricing_services->account_id=(Auth::user()->id_account);
        $pricing_services->save();
         return  redirect()->back()->with('success', trans('message_trans.save'));


       
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
     $pricing=pricing_settings::find($id);
     $services=services_settings::all();
     $pricing_services_settings=pricing_services::where('id_pricing',$id)->get();
     return view('settings.accounts.pricing.add_services',compact('pricing','services','pricing_services_settings'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Storepricing_services $request)
  {
        try
        {
        $validated = $request->validated();
        $pricing_services_settings= pricing_services_settings::findorFail($request->id);
        $pricing_services_settings->update([
        $pricing_services_settings->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
        $pricing_services_settings->phone=$request->phone,
        $pricing_services_settings->commercial_register=$request->commercial_register,
        $pricing_services_settings->tax_card=$request->tax_card,
        $pricing_services_settings->address=$request->address,
        $pricing_services_settings->name_manager=$request->name_manager,
        ]);
         return redirect()->route('pricing_services_settings.index')->with('success', trans('message_trans.updated'));


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

      $pricing_services_settings= pricing_services::findorFail($request->id)->delete();
        return  redirect()->back()->with('success', trans('message_trans.delete'));

    }
    catch(\Exception $e)
    {
        return  redirect()->back()->with('info', trans('message_trans.exists'));
    }
  }

}

?>
