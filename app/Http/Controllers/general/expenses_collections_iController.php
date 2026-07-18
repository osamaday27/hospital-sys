<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\models\expenses_collections_i;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class expenses_collections_iController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $expenses_collections_i=expenses_collections_i::where('account_id',Auth::user()->id_account)->get();
    return view('settings.accounts.expenses_collections_i.index',compact('expenses_collections_i'));

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

    if(expenses_collections_i::where('name->ar',$request->name_ar)
    ->where('name->en',$request->name_en)
    ->where('type',$request->type)
    ->where('account_id',Auth::user()->id_account)
    ->exists())
    {
       session()->flash('add');
       return redirect()->route('expenses_collections_i.index');
    }
    try
    {
    
        $expenses_collections_i=new expenses_collections_i();
        $expenses_collections_i->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $expenses_collections_i->type=$request->type;
        $expenses_collections_i->user_id=(Auth::user()->id);
        $expenses_collections_i->account_id=(Auth::user()->id_account);
        $expenses_collections_i->save();
        session()->flash('add');
        return redirect()->route('expenses_collections_i.index');
       
    }catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  
  }

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
    $expenses_collections_i=expenses_collections_i::find($id);
    return view('settings.accounts.expenses_collections_i.edit',compact('expenses_collections_i'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request)
  {
        try
        {
        $expenses_collections_i= expenses_collections_i::findorFail($request->id);
        $expenses_collections_i->update([
            $expenses_collections_i->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
            $expenses_collections_i->type=$request->type,
        ]);
         session()->flash('uploaded_m');
         return redirect()->route('expenses_collections_i.index');
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
      $expenses_collections_i= expenses_collections_i::findorFail($request->id)->delete();
      session()->flash('delete_m');
      return redirect()->route('expenses_collections_i.index');
    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }
 
}

?>