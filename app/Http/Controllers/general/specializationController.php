<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storespecialization;
use App\models\general\specialties;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class specializationController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $specialization=specialization::all();
    return view('settings.specialization',compact('specialization'));

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
  public function store(Storespecialization $request)
  {
    

   
    if(specialization::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
    {
        return  redirect()->back()->withErrors([trans('specialization_trans.existes') ]);
    }
    try
    {
        $validated = $request->validated();
        $specialization=new specialization();
        $specialization->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $specialization->status=1;
        $specialization->icon=1;
        $specialization->account_id=(Auth::user()->id_account);
        $specialization->save();
        session()->flash('add');
        return redirect()->route('specialization.index');
       
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
  public function update(Storespecialization $request)
  {
        try
        {
        $validated = $request->validated();
        $specialization= specialization::findorFail($request->id);
        $specialization->update([
            $specialization->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
        ]);
        session()->flash('edit_m');
        return redirect()->route('specialization.index');

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

      $specialization= specialization::findorFail($request->id)->delete();
      return redirect()->route('specialization.index')->with('success', 'User Deleted successfully.');

    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }

}

?>