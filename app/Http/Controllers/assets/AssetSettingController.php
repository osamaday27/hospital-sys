<?php
namespace App\Http\Controllers\assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\assets\assetsetting;

class AssetSettingController extends Controller
{
        public function edit(){
        $row = assetsetting::first();
        if(!$row){ $row = assetsetting::create([]); }
        return view('assets.settings.general.edit', compact('row'));
    }

    public function update(Request $r){
        $row = assetsetting::firstOrFail();
        $data = $r->validate([
            'code_prefix'=>'required|max:10',
            'qr_size_px'=>'required|integer|min:64|max:1024',
            'qr_margin'=>'required|integer|min:0|max:20',
            'enable_rfid'=>'nullable|boolean',
            'qr_disk'=>'required|string',
            'qr_path'=>'required|string'
        ]);
        $data['enable_rfid'] = (bool)($r->enable_rfid ?? 0);
        $row->update($data);

        $action = $r->input('submit_action','stay'); // لا يوجد index عام؛ الافتراضي البقاء
        if ($action === 'back') {
            // صفحة إعدادات الموديول العامة حسب routes التي زودتها:
            return redirect()->route('settings_programs')->with('success','تم الحفظ.');
        }
        return redirect()->route('assets.settings.general.edit')->with('success','تم الحفظ وبقيت هنا.');
    }
}
