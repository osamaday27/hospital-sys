<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCountry;
use App\models\rad_settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class rad_settingsController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $rad=rad_settings::all();
    return view('settings.medical_data.rad.index',compact('rad'));

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
  public function store(StoreCountry $request)
  {
    

   
    if(rad_settings::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
    {
      session()->flash('add');
      return redirect()->route('rad_settings.index');
    }
    try
    {
        $validated = $request->validated();
        $rad=new rad_settings();
        $rad->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $rad->description=$request->description;
        $rad->preparations=$request->preparations;
        $rad->active=1;
        $rad->user_add=(Auth::user()->id);
        $rad->account_id=(Auth::user()->id_account);
        $rad->save();
        session()->flash('add');
        return redirect()->route('rad_settings.index');
       
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
    $rad=rad_settings::find($id);
    return view('settings.medical_data.rad.edit',compact('rad'));
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

      $rad_settings= rad_settings::findorFail($request->id)->delete();
      session()->flash('delete_m');
      return redirect()->route('rad_settings.index');

    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }
  public function update_favorite($id)
  {
        try
        {
        $favorite='';
        $rad_settings= rad_settings::findorFail($id);
        if($rad_settings->favorite==1)
        {
          $favorite='0';
        }else
        {
           $favorite='1';
        }
        $rad_settings->update([$rad_settings->favorite=$favorite]);
        return response(['success' => 'Lab Test created successfully.']);

        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
        }

  }
}

?>