<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\supply_chain\productdata;

class ProductLookupController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = productdata::query()
            ->select('id','name_ar','name_en')
            ->where('status', 'active');

        if ($q !== '') {
            $query->where(function($qq) use ($q){
                $qq->where('name_ar', 'like', "%{$q}%")
                   ->orWhere('name_en', 'like', "%{$q}%")
                   ->orWhere('product_code', 'like', "%{$q}%")
                   ->orWhere('barcode', 'like', "%{$q}%")
                   ->orWhere('unified_code', 'like', "%{$q}%");
            });
        }

        return response()->json(
            $query->orderByDesc('id')->limit(20)->get()
        );
    }
}
