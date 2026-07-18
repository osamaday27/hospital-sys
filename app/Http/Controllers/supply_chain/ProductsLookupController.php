<?php

namespace App\Http\Controllers\SupplyChain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\supply_chain\productdata;

class ProductsLookupController extends Controller
{
    public function search(Request $request)
    {
        $term = trim($request->get('term', ''));

        $query = productdata::query()
            ->select('id', 'name_ar', 'name_en', 'product_code', 'barcode')
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function($qq) use ($term) {
                    $qq->where('name_ar', 'like', "%{$term}%")
                       ->orWhere('name_en', 'like', "%{$term}%")
                       ->orWhere('product_code', 'like', "%{$term}%")
                       ->orWhere('barcode', 'like', "%{$term}%");
                });
            })
            ->orderBy('name_ar')
            ->limit(20)
            ->get();

        $results = $query->map(function($p){
            $label = "{$p->name_ar} / {$p->name_en}";
            if ($p->product_code) $label = "{$p->product_code} — {$label}";
            return ['id' => $p->id, 'text' => $label];
        });

        // Select2 يتوقع {results:[...]}
        return response()->json(['results' => $results]);
    }
}
