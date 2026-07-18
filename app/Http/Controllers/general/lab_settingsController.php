<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCountry;
use App\models\lab_settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class lab_settingsController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $lab=lab_settings::all();
    return view('settings.medical_data.lab.index',compact('lab'));

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
    

   
    if(lab_settings::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
    {
        return  redirect()->back()->withErrors([trans('Country_trans.existes') ]);
    }
    try
    {
        $validated = $request->validated();
        $lab=new lab_settings();
        $lab->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $lab->description=$request->description;
        $lab->preparations=$request->preparations;
        $lab->active=1;
        $lab->user_add=(Auth::user()->id);
        $lab->account_id=(Auth::user()->id_account);
        $lab->save();
        session()->flash('add');
        return redirect()->route('lab_settings.index');
       
    }catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  
  }
   public function save(Request $request)
    {
       
        if(lab_settings::where('name->ar',$request->input('name_test'))->orwhere('name->en',$request->input('name_test'))->exists())
          {
              return response(['exists' =>'This data already exists']);
          }
        try {
          $lab=new lab_settings();
          $lab->name=['en'=>$request->input('name_test'),'ar'=>$request->input('name_test')];
          $lab->active=1;
          $lab->user_add=(Auth::user()->id);
          $lab->account_id=(Auth::user()->id_account);
          $lab->save();
            return response(['success' => 'Lab Test created successfully.']);
        } catch (\Exception $e) {
          
            return response(['mas' => $e->getMessage()]);
        }
    }

  public function update_favorite($id)
  {
        try
        {
        $favorite='';
        $lab_settings= lab_settings::findorFail($id);
        if($lab_settings->favorite==1)
        {
          $favorite='0';
        }else
        {
           $favorite='1';
        }
        $lab_settings->update([$lab_settings->favorite=$favorite]);
        return response(['success' => 'Lab Test created successfully.']);

        }
        catch(\Exception $e)
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
    $lab=lab_settings::find($id);
    return view('settings.medical_data.lab.edit',compact('lab'));
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
        $lab_settings= lab_settings::findorFail($request->id);
        $lab_settings->update([
            $lab_settings->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
            $lab_settings->description=$request->description,
            $lab_settings->preparations=$request->preparations,
        ]);
        session()->flash('uploaded_m');
         return redirect()->route('lab_settings.index');
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

      $lab_settings= lab_settings::findorFail($request->id)->delete();
      session()->flash('delete_m');
      return redirect()->route('lab_settings.index');

    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }

}

?>