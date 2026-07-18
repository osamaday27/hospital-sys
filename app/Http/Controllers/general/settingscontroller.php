<?php

namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class settingscontroller extends Controller
{
    public function index()
    {
        return view('users.system_settings');
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        // Update user settings
        $user->update([
           ($user->data_layout = $request->input('data-layout')??'vertical'), 
            ($user->data_topbar = $request->input('data-topbar')??'light'), 
            ($user->data_sidebar = $request->input('data-sidebar')??'light'), 
            ($user->data_sidebar_size = $request->input('data-sidebar-size')??'lg'), 
            ($user->data_sidebar_image = $request->input('data-layout')??'none'), 
            ($user->data_preloader = $request->input('data-preloader')??'disable'), 
            ($user->data_layout_mode = $request->input('data-layout-mode')??'light'), 
            ($user->data_layout_width = $request->input('data-layout-width')??'fluid'), 
            ($user->data_layout_position = $request->input('data-layout-position')??'fixed'), 
            ($user->data_layout_style = $request->input('data-layout-style')??'default'), 
            ($user->data_sidebar_visibility = $request->input('data-sidebar-visibility')??'show'), 

        ]);
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}

?>