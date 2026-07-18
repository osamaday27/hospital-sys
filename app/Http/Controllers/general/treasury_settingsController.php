<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCountry;
use App\models\treasury_settings;
use App\models\currencies;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class treasury_settingsController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $treasury_settings=treasury_settings::where('account_id',Auth::user()->id_account)->get();
    $currencies=currencies::where('account_id',Auth::user()->id_account)->get();
    return view('settings.accounts.treasury.index',compact('treasury_settings','currencies'));

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
  public function store(Request $request)
  {
    

   
    if(treasury_settings::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
    {
      session()->flash('add');
      return redirect()->route('treasury_settings.index');
    }
    try
    {
    
        $treasury_settings=new treasury_settings();
        $treasury_settings->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $treasury_settings->type=$request->type;
        $treasury_settings->branches=$request->branches;
        $treasury_settings->currency=$request->currency;
        $treasury_settings->id_user=(Auth::user()->id);
        $treasury_settings->account_id=(Auth::user()->id_account);
        $treasury_settings->save();
        session()->flash('add');
        return redirect()->route('treasury_settings.index');
       
    }catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  
  }
  public function save(Request $request)
    {
       
        if(rad_settings::where('name->ar',$request->input('name_rad'))->orwhere('name->en',$request->input('name_rad'))->exists())
          {
              return response(['exists' =>'This data already exists']);
          }
        try {
          $rad=new rad_settings();
          $rad->name=['en'=>$request->input('name_rad'),'ar'=>$request->input('name_rad')];
          $rad->active=1;
          $rad->user_add=(Auth::user()->id);
          $rad->account_id=(Auth::user()->id_account);
          $rad->save();
            return response(['success' => 'rad Test created successfully.']);
        } catch (\Exception $e) {
          
            return response(['mas' => $e->getMessage()]);
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
    $treasury_settings=treasury_settings::find($id);
    $currencies=currencies::where('account_id',Auth::user()->id_account)->get();
    return view('settings.accounts.treasury.edit',compact('treasury_settings','currencies'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(StoreCountry $request)
  {
        try
        {
        $validated = $request->validated();
        $rad_settings= rad_settings::findorFail($request->id);
        $rad_settings->update([
            $rad_settings->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
            $rad_settings->description=$request->description,
            $rad_settings->preparations=$request->preparations,
        ]);
        session()->flash('uploaded_m');
         return redirect()->route('rad_settings.index');
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

      $treasury_settings= treasury_settings::findorFail($request->id)->delete();
      session()->flash('delete_m');
      return redirect()->route('treasury_settings.index');

    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }
 
}

?>