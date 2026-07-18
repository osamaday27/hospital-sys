<?php
namespace App\Http\Controllers\finance\costing;
use App\Http\Controllers\Controller;
class costing_programscontroller extends Controller
{
    public function index()
    {
        return view('finance.costing.program.index');
    }
    public function costing_run_wizard()
    {
        return view('finance.costing.program.costing_run_wizard');
    }
    public function costing_batches()
    {
        return view('finance.costing.program.costing_batches');
    }
    public function costing_adjustments()
    {
        return view('finance.costing.program.costing_adjustments');
    }
    public function costing_posting()
    {
        return view('finance.costing.program.costing_posting');
    }
}