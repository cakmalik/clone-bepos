<?php

namespace App\Http\Controllers\API\Minimarket;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{

    public function index()
    {
        return responseAPI(
            true,
            'berhasil',
            Setting::all()
        );
    }
    public function isNeedValidationWhenRemoveItem()
    {
        return responseAPI(
            true,
            'berhasill',
            !!Setting::where('name', 'superior_validation')->first()->value
        );
    }
}
