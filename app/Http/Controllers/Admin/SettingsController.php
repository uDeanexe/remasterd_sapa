<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Division;
use App\Models\OfficeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $tab = (string) $request->query('tab', 'attendance');
        $tab = in_array($tab, ['user', 'attendance', 'jobs'], true) ? $tab : 'attendance';

        $officeSetting = OfficeSetting::query()->first();

        $userSetting = AppSetting::query()->firstOrCreate(
            ['key' => 'user_defaults'],
            ['value' => [
                'default_password' => 'jonusa123',
                'requires_location' => true,
                'radius_meters' => 100,
            ]]
        );

        $divisions = Division::query()->orderBy('name')->get();

        return view('setting.index', [
            'tab' => $tab,
            'officeSetting' => $officeSetting,
            'userSetting' => $userSetting,
            'divisions' => $divisions,
        ]);
    }

    public function updateAttendance(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'radius' => ['required', 'numeric', 'min:1', 'max:100000'],
            'radius_enforced' => ['nullable', 'boolean'],
            'check_in_time' => ['required', 'regex:/^\\d{2}:\\d{2}$/'],
            'check_out_time' => ['required', 'regex:/^\\d{2}:\\d{2}$/'],
            'late_tolerance' => ['required', 'integer', 'min:0', 'max:240'],
        ]);

        $setting = OfficeSetting::query()->firstOrNew(['id' => 1]);
        $setting->fill([
            'name' => $validated['name'] ?? $setting->name,
            'latitude' => $validated['latitude'] ?? $setting->latitude,
            'longitude' => $validated['longitude'] ?? $setting->longitude,
            'radius' => $validated['radius'],
            'radius_enforced' => (bool) ($validated['radius_enforced'] ?? false),
            'check_in_time' => $validated['check_in_time'],
            'check_out_time' => $validated['check_out_time'],
            'late_tolerance' => $validated['late_tolerance'],
        ]);
        $setting->save();

        return redirect()->route('admin.settings', ['tab' => 'attendance'])->with('success', 'Absensi setting berhasil diperbarui.');
    }

    public function updateUser(Request $request)
    {
        $validated = $request->validate([
            'default_password' => ['required', 'string', 'min:6', 'max:100'],
            'requires_location' => ['nullable', 'boolean'],
            'radius_meters' => ['required', 'integer', 'min:10', 'max:10000'],
        ]);

        $setting = AppSetting::query()->firstOrCreate(['key' => 'user_defaults']);

        $setting->update([
            'value' => [
                'default_password' => $validated['default_password'],
                'requires_location' => (bool) ($validated['requires_location'] ?? false),
                'radius_meters' => (int) $validated['radius_meters'],
            ],
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.settings', ['tab' => 'user'])->with('success', 'User setting berhasil diperbarui.');
    }

    public function updateJobs(Request $request)
    {
        $validated = $request->validate([
            'division_id' => ['required', 'integer', Rule::exists('divisions', 'id')],
            'step_1' => ['required', 'string', 'max:255'],
            'step_2' => ['required', 'string', 'max:255'],
            'step_3' => ['required', 'string', 'max:255'],
            'step_4' => ['required', 'string', 'max:255'],
            'req_desc_1' => ['nullable', 'boolean'],
            'req_desc_2' => ['nullable', 'boolean'],
            'req_desc_3' => ['nullable', 'boolean'],
            'req_desc_4' => ['nullable', 'boolean'],
            'req_photo_1' => ['nullable', 'boolean'],
            'req_photo_2' => ['nullable', 'boolean'],
            'req_photo_3' => ['nullable', 'boolean'],
            'req_photo_4' => ['nullable', 'boolean'],
            'req_video_1' => ['nullable', 'boolean'],
            'req_video_2' => ['nullable', 'boolean'],
            'req_video_3' => ['nullable', 'boolean'],
            'req_video_4' => ['nullable', 'boolean'],
        ]);

        $division = Division::query()->findOrFail((int) $validated['division_id']);

        $division->update([
            'step_1' => $validated['step_1'],
            'step_2' => $validated['step_2'],
            'step_3' => $validated['step_3'],
            'step_4' => $validated['step_4'],
            'req_desc_1' => (bool) ($validated['req_desc_1'] ?? false),
            'req_desc_2' => (bool) ($validated['req_desc_2'] ?? false),
            'req_desc_3' => (bool) ($validated['req_desc_3'] ?? false),
            'req_desc_4' => (bool) ($validated['req_desc_4'] ?? false),
            'req_photo_1' => (bool) ($validated['req_photo_1'] ?? false),
            'req_photo_2' => (bool) ($validated['req_photo_2'] ?? false),
            'req_photo_3' => (bool) ($validated['req_photo_3'] ?? false),
            'req_photo_4' => (bool) ($validated['req_photo_4'] ?? false),
            'req_video_1' => (bool) ($validated['req_video_1'] ?? false),
            'req_video_2' => (bool) ($validated['req_video_2'] ?? false),
            'req_video_3' => (bool) ($validated['req_video_3'] ?? false),
            'req_video_4' => (bool) ($validated['req_video_4'] ?? false),
        ]);

        return redirect()->route('admin.settings', ['tab' => 'jobs'])->with('success', 'Pekerjaan setting berhasil diperbarui.');
    }
}
