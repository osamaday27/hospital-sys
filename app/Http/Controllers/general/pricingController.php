<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storepricing;
use App\models\pricing_settings;
use App\models\services_settings;
use App\models\pricing_services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class pricingController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $pricing=pricing_settings::all();
    return view('settings.accounts.pricing.index',compact('pricing'));

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
  public function store(Storepricing $request)
  {
    

   
    if(pricing_settings::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
    {
        return  redirect()->back()->withErrors([trans('pricing_trans.existes') ]);
    }
    try
    {
        $validated = $request->validated();
        $pricing=new pricing_settings();
        $pricing->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $pricing->status=$request->status;
        $pricing->user_id=(Auth::user()->id);
        $pricing->account_id=(Auth::user()->id_account);
        $pricing->save();
        return redirect()->route('pricing_settings.index')->with('success', trans('message_trans.save'));

       
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
    $pricing=pricing_settings::find($id);
    $services=services_settings::all();
    $pricing_services_settings=pricing_services::where('id_pricing',$id)->get();
    return view('settings.accounts.pricing.add_services',compact('pricing','services','pricing_services_settings'));
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
    return view('settings.accounts.pricing.edit',compact('pricing'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Storepricing $request)
  {
        try
        {
        $validated = $request->validated();
        $pricing_settings= pricing_settings::findorFail($request->id);
        $pricing_settings->update([
        $pricing_settings->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
        $pricing_settings->status=$request->status,
        ]);
         return redirect()->route('pricing_settings.index')->with('success', trans('message_trans.updated'));
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
      $pricing_settings= pricing_settings::findorFail($request->id)->delete();
       return redirect()->route('pricing_settings.index')->with('success', trans('message_trans.delete'));
    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }

}

?>
