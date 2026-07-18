<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class return_itemscontroller extends Controller
{
    public function index(){
        return view('supply_chain.program.return_items.index');
    }
    public function return_to_store(){
        return view('supply_chain.program.return_items.return_to_store.index');
    }        public function return_from_section(){
        return view('supply_chain.program.return_items.return_from_section.index');
    }        public function return_from_client(){
        return view('supply_chain.program.return_items.return_from_client.index');
    }
}
