<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storecompany;
use App\models\company_settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class company_settingsController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $company=company_settings::all();
    return view('settings.accounts.company.index',compact('company'));

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
  public function store(Storecompany $request)
  {
    

   
    if(company_settings::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
    {
        return  redirect()->back()->withErrors([trans('company_trans.existes') ]);
    }
    try
    {
        $validated = $request->validated();
        $company=new company_settings();
        $company->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $company->phone=$request->phone;
        $company->commercial_register=$request->commercial_register;
        $company->tax_card=$request->tax_card;
        $company->address=$request->address;
        $company->name_manager=$request->name_manager;
        $company->user_id=(Auth::user()->id);
        $company->account_id=(Auth::user()->id_account);
        $company->save();
        session()->flash('add');
        return redirect()->route('company_settings.index');
       
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
    $company=company_settings::find($id);
    return view('settings.accounts.company.edit',compact('company'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Storecompany $request)
  {
        try
        {
        $validated = $request->validated();
        $company_settings= company_settings::findorFail($request->id);
        $company_settings->update([
        $company_settings->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
        $company_settings->phone=$request->phone,
        $company_settings->commercial_register=$request->commercial_register,
        $company_settings->tax_card=$request->tax_card,
        $company_settings->address=$request->address,
        $company_settings->name_manager=$request->name_manager,
        ]);
         return redirect()->route('company_settings.index')->with('success', trans('message_trans.updated'));


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

      $company_settings= company_settings::findorFail($request->id)->delete();
     return redirect()->route('company_settings.index')->with('success', trans('message_trans.delete'));

    }
    catch(\Exception $e)
    {
         return redirect()->route('company_settings.index')->with('success', $e);
    }
  }

}

?>
