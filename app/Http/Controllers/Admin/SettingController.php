<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display general system settings.
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $val) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($val) ? json_encode($val) : (string)$val]
            );
        }

        return back()->with('success', 'System settings updated successfully.');
    }
}

