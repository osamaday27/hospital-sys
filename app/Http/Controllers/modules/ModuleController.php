<?php

namespace App\Http\Controllers\modules;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * عرض صفحة الموديولات حسب القسم
     */
    public function show($section)
    {
        $availableSections = [
            'medical',
            'finance',
            'admin',
            'requests'
        ];

        // لو القسم مش موجود → رجّع 404
        if (!in_array($section, $availableSections)) {
            abort(404);
        }

        // صفحة العرض تكون في resources/views/modules/{section}.blade.php
        return view('modules.' . $section, compact('section'));
    }
}