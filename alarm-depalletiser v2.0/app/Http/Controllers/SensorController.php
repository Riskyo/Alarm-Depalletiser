<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function store(Request $request, Action $action)
{
    $request->validate([
        'sensor_name' => 'required|string',
        'komponen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        'plc_io' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096'
    ]);

    $komponenPath = $request->file('komponen')?->store('komponen','public');
    $plcPath = $request->file('plc_io')?->store('plc_io','public');

    $action->sensors()->create([
        'sensor_name' => $request->sensor_name,
        'komponen' => $komponenPath,
        'plc_io' => $plcPath,
    ]);

    return back()->with('success','Sensor ditambahkan');
}

}
