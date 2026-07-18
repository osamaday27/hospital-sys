<?php
namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use App\Traits\web\file_storage;
use App\Models\supply_chain\storemovement;
use App\Helpers\NumberHelper;
class store_programcontroller extends Controller
{
    use file_storage;

    public function add_items()
    {
        return view('supply_chain.program.add_items');
    }
    public function opening_balance()
    {
        return view('supply_chain.program.opening_balance');
    } public function transfer_substore()
    {
        return view('supply_chain.program.transfer_substore');
    }

    public function opening_balanceview()
    {
        return view('supply_chain.program.opening_balanceview');
    }
    public function storemovement()
    {
        return view('supply_chain.program.storemovement');
    }
    public function withdrawal_requests()
    {
        return view('supply_chain.program.withdrawal_requests');
    }
        public function new_item_request()
    {
        return view('supply_chain.program.new_item_request');
    }
    public function receiving_inspecting()
    {
        return view('supply_chain.program.receiving_inspecting');
    }
    public function show($id)
    {
        return view('supply_chain.program.storemovementdetails', compact('id'));
    }
    public function edit($id)
    {
        return view('supply_chain.program.updatemovment', compact('id'));
    }
    public function print($movement_number)
    {
        $movement_summary = storemovement::where('movement_number', $movement_number)
            ->with(['store', 'supplier', 'user'])
            ->firstOrFail();

        $movement_items = storemovement::where('movement_number', $movement_number)
            ->with(['product', 'unit'])
            ->get();

        return view('supply_chain.program.printmovement', compact('movement_summary', 'movement_items'));
    }
    public function printopening_balance($movement_number)
    {
        $movement_summary = storemovement::where('movement_number', $movement_number)
            ->with(['store', 'supplier', 'user'])
            ->firstOrFail();

        $movement_items = storemovement::where('movement_number', $movement_number)
            ->with(['product', 'unit'])
            ->get();

        return view('supply_chain.program.printopening_balance', compact('movement_summary', 'movement_items'));
    }
    public function showopening_balance($id)
    {
        return view('supply_chain.program.opening_balancedetails', compact('id'));
    }
    public function editopening_balance($id)
    {
        return view('supply_chain.program.updateopening_balance', compact('id'));
    }
}
