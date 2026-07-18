<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storecontract;
use App\models\contract_settings;
use App\models\company_settings;
use App\models\pricing_settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class contractController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $company=company_settings::all();
    $contract=contract_settings::all();
    $pricing=pricing_settings::all();
    return view('settings.accounts.contract.index',compact('contract','pricing','company'));

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
  public function store(Storecontract $request)
  {
    

   
    if(contract_settings::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
    {
        return  redirect()->back()->withErrors([trans('contract_trans.existes') ]);
    }
    try
    {
        $validated = $request->validated();
        $contract=new contract_settings();
        $contract->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $contract->id_company=$request->id_company;
        $contract->id_pricing=$request->id_pricing;
        $contract->discount=$request->discount;
        $contract->discount_type=$request->discount_type;
        $contract->patient_contribution=$request->patient_contribution;
        $contract->patient_contribution_type=$request->patient_contribution_type;
        $contract->status=1;
        $contract->user_id=(Auth::user()->id);
        $contract->account_id=(Auth::user()->id_account);
        $contract->save();
        
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
      $contract=contract_settings::find($id);
      $company=company_settings::all();
      $pricing=pricing_settings::all();
      return view('settings.accounts.contract.edit',compact('contract','pricing','company'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Storecontract $request)
  {
        try
        {
        $validated = $request->validated();
        $contract= contract_settings::findorFail($request->id);
        $contract->update([
        $contract->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
        $contract->id_company=$request->id_company,
        $contract->id_pricing=$request->id_pricing,
        $contract->discount=$request->discount,
        $contract->discount_type=$request->discount_type,
        $contract->patient_contribution=$request->patient_contribution,
        $contract->patient_contribution_type=$request->patient_contribution_type,
        $contract->status=$request->status,
        ]);
         return redirect()->route('contract_settings.index')->with('success', trans('message_trans.updated'));


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

      $contract_settings= contract_settings::findorFail($request->id)->delete();
     return redirect()->route('contract_settings.index')->with('success', trans('message_trans.delete'));

    }
    catch(\Exception $e)
    {
         return redirect()->route('contract_settings.index')->with('success', $e);
    }
  }

}

?>
