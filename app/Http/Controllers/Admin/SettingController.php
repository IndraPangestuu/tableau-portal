<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name' => Setting::get('app_name', 'DAKGAR LANTAS'),
            'app_subtitle' => Setting::get('app_subtitle', 'Dashboard Portal'),
            'app_logo' => Setting::get('app_logo'),
            'app_favicon' => Setting::get('app_favicon'),
            'footer_text' => Setting::get('footer_text', 'KORLANTAS POLRI'),
            'dashboard_refresh_interval' => Setting::get('dashboard_refresh_interval', 0),
            'enable_fullscreen' => Setting::get('enable_fullscreen', true),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:100',
            'app_subtitle' => 'nullable|string|max:100',
            'footer_text' => 'nullable|string|max:100',
            'dashboard_refresh_interval' => 'nullable|integer|min:0|max:3600',
            'app_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'app_favicon' => 'nullable|image|mimes:png,ico|max:512',
        ]);

        // Text settings
        Setting::set('app_name', $request->app_name);
        Setting::set('app_subtitle', $request->app_subtitle);
        Setting::set('footer_text', $request->footer_text);
        Setting::set('dashboard_refresh_interval', $request->dashboard_refresh_interval ?? 0, 'integer', 'dashboard');
        Setting::set('enable_fullscreen', $request->has('enable_fullscreen') ? '1' : '0', 'boolean', 'dashboard');

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo && file_exists(base_path($oldLogo))) {
                @unlink(base_path($oldLogo));
            }

            $file = $request->file('app_logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Create directory if not exists
            $uploadPath = base_path('uploads/settings');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $filename);
            Setting::set('app_logo', 'uploads/settings/' . $filename);
        }

        // Handle favicon upload
        if ($request->hasFile('app_favicon')) {
            $oldFavicon = Setting::get('app_favicon');
            if ($oldFavicon && file_exists(base_path($oldFavicon))) {
                @unlink(base_path($oldFavicon));
            }

            $file = $request->file('app_favicon');
            $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Create directory if not exists
            $uploadPath = base_path('uploads/settings');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $filename);
            Setting::set('app_favicon', 'uploads/settings/' . $filename);
        }

        // Remove logo if requested
        if ($request->has('remove_logo')) {
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo && file_exists(base_path($oldLogo))) {
                @unlink(base_path($oldLogo));
            }
            Setting::set('app_logo', null);
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
