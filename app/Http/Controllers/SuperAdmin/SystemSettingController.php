<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SystemSettingController extends Controller
{
    /**
     * Tampilkan semua pengaturan sistem
     */
    public function index()
    {
        $settings = SystemSetting::all()->pluck('setting_value', 'setting_key')->toArray();
        return view('superadmin.settings.index', compact('settings'));
    }

    /**
     * Update banyak setting sekaligus
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kuesioner_status' => 'required|in:open,closed',
            'app_name' => 'nullable|string|max:100',
            'app_version' => 'nullable|string|max:20',
            'target_jurusan' => 'nullable|string|max:50',
            'tujuan_kuesioner' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        foreach ($request->except('_token') as $key => $value) {
            SystemSetting::set($key, $value);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan']);
        }
        return back()->with('success', 'Pengaturan berhasil disimpan');
    }

    /**
     * Update single setting via key
     */
    public function updateSingle(Request $request, $key)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator);
        }

        SystemSetting::set($key, $request->value);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Setting {$key} berhasil diupdate"]);
        }
        return back()->with('success', "Setting {$key} berhasil diupdate");
    }
}