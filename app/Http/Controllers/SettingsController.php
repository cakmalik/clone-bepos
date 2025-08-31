<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Setting;
use App\Models\SettingsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Settings;

class SettingsController extends Controller
{

    public function index()
    {
        return view('pages.setting.pos.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSettingRequest  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */

    public function stock_alert(Request $request)
    {
        DB::beginTransaction();

        try {
            $setting = Setting::where('id', $request->id)->first();

            if ($request->status == "true") {
                Setting::where('id', $request->id)->update([
                    'value' => true
                ]);
            } else {
                Setting::where('id', $request->id)->update([
                    'value' => false
                ]);
            }

            $setting_after = Setting::where('id', $request->id)->first();

            $settingsLog =  new SettingsLog();
            $settingsLog->user_id = Auth()->user()->id;
            $settingsLog->name =  $setting->name;
            $settingsLog->model = '\App\Model\Setting';
            $settingsLog->status =  $setting_after->value;
            $settingsLog->old_data =  $setting;
            $settingsLog->current_data = $setting_after;

            $settingsLog->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Gagal!',
            ], 422);
        }
    }

    public function stock_minus(Request $request)
    {
        DB::beginTransaction();

        try {
            $setting = Setting::where('id', $request->id)->first();

            if ($request->status == "true") {
                Setting::where('id', $request->id)->update([
                    'value' => true
                ]);
            } else {
                Setting::where('id', $request->id)->update([
                    'value' => false
                ]);
            }

            $setting_after = Setting::where('id', $request->id)->first();

            $settingsLog =  new SettingsLog();
            $settingsLog->user_id = Auth()->user()->id;
            $settingsLog->name =  $setting->name;
            $settingsLog->model = '\App\Model\Setting';
            $settingsLog->status =  $setting_after->value;
            $settingsLog->old_data =  $setting;
            $settingsLog->current_data = $setting_after;

            $settingsLog->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Gagal!',
            ], 422);
        }
    }
    public function superior_validation(Request $request)
    {
        DB::beginTransaction();

        try {
            $setting = Setting::where('id', $request->id)->first();

            if ($request->status == "true") {
                $setting->value = true;
            } else {
                $setting->value = false;
            }
            $setting = Setting::where('id', $request->id)->first();

            if ($request->status == "true") {
                Setting::where('id', $request->id)->update([
                    'value' => true
                ]);
            } else {
                Setting::where('id', $request->id)->update([
                    'value' => false
                ]);
            }

            $setting_after = Setting::where('id', $request->id)->first();

            $settingsLog =  new SettingsLog();
            $settingsLog->user_id = Auth()->user()->id;
            $settingsLog->name =  $setting->name;
            $settingsLog->model = '\App\Model\Setting';
            $settingsLog->status =  $setting_after->value;
            $settingsLog->old_data =  $setting;
            $settingsLog->current_data = $setting_after;

            $settingsLog->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Gagal!',
            ], 422);
        }
    }
    public function minus_price(Request $request)
    {
        DB::beginTransaction();

        try {
            $setting = Setting::where('id', $request->id)->first();

            if ($request->status == "true") {
                Setting::where('id', $request->id)->update([
                    'value' => true
                ]);
            } else {
                Setting::where('id', $request->id)->update([
                    'value' => false
                ]);
            }

            $setting_after = Setting::where('id', $request->id)->first();

            $settingsLog =  new SettingsLog();
            $settingsLog->user_id = Auth()->user()->id;
            $settingsLog->name =  $setting->name;
            $settingsLog->model = '\App\Model\Setting';
            $settingsLog->status =  $setting_after->value;
            $settingsLog->old_data =  $setting;
            $settingsLog->current_data = $setting_after;

            $settingsLog->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Gagal!',
            ], 422);
        }
    }
    public function price_change(Request $request)
    {
        DB::beginTransaction();

        try {
            $setting = Setting::where('id', $request->id)->first();

            if ($request->status == "true") {
                Setting::where('id', $request->id)->update([
                    'value' => true
                ]);
            } else {
                Setting::where('id', $request->id)->update([
                    'value' => false
                ]);
            }

            $setting_after = Setting::where('id', $request->id)->first();

            $settingsLog =  new SettingsLog();
            $settingsLog->user_id = Auth()->user()->id;
            $settingsLog->name =  $setting->name;
            $settingsLog->model = '\App\Model\Setting';
            $settingsLog->status =  $setting_after->value;
            $settingsLog->old_data =  $setting;
            $settingsLog->current_data = $setting_after;

            $settingsLog->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Gagal!',
            ], 422);
        }
    }
    public function show_recent_sales(Request $request)
    {
        DB::beginTransaction();

        try {
            $setting = Setting::where('id', $request->id)->first();

            if ($request->status == "true") {
                Setting::where('id', $request->id)->update([
                    'value' => true
                ]);
            } else {
                Setting::where('id', $request->id)->update([
                    'value' => false
                ]);
            }

            $setting_after = Setting::where('id', $request->id)->first();

            $settingsLog =  new SettingsLog();
            $settingsLog->user_id = Auth()->user()->id;
            $settingsLog->name =  $setting->name;
            $settingsLog->model = '\App\Model\Setting';
            $settingsLog->status =  $setting_after->value;
            $settingsLog->old_data =  $setting;
            $settingsLog->current_data = $setting_after;

            $settingsLog->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Gagal!',
            ], 422);
        }
    }
    public function change_qty_direct_after_add(Request $request)
    {
        DB::beginTransaction();

        try {
            $setting = Setting::where('id', $request->id)->first();

            if ($request->status == "true") {
                Setting::where('id', $request->id)->update([
                    'value' => true
                ]);
            } else {
                Setting::where('id', $request->id)->update([
                    'value' => false
                ]);
            }

            $setting_after = Setting::where('id', $request->id)->first();

            $settingsLog =  new SettingsLog();
            $settingsLog->user_id = Auth()->user()->id;
            $settingsLog->name =  $setting->name;
            $settingsLog->model = '\App\Model\Setting';
            $settingsLog->status =  $setting_after->value;
            $settingsLog->old_data =  $setting;
            $settingsLog->current_data = $setting_after;

            $settingsLog->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Gagal!',
            ], 422);
        }
    }
}
