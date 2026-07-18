<?php
namespace App\Http\Controllers\finance\costing;
use App\Http\Controllers\Controller;
class costing_settingscontroller extends Controller
{
    public function index()
    {
        return view('finance.costing.settings.index');
    }
    public function cost_centers()
    {
        return view('finance.costing.settings.cost_centers');
    }
    public function overhead_pools()
    {
        return view('finance.costing.settings.overhead_pools');
    }
    public function overhead_allocation()
    {
        return view('finance.costing.settings.overhead_allocation');
    }
     public function resource_rates()
    {
        return view('finance.costing.settings.resource_rates');
    }
     public function service_boms()
    {
        return view('finance.costing.settings.service_boms');
    }
       public function cost_center_linking()
    {
        return view('finance.costing.settings.cost_center_linking');
    }
}