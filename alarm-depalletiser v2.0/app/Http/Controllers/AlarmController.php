<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Action;
use App\Models\Sensor;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    // Publik: lihat & cari
    public function index(Request $request)
    {
        $search = trim((string)$request->input('search'));
        // default jadi 'asc' supaya paling lama dulu
        $sort   = $request->input('sort', 'asc');
    
        $alarms = Alarm::with('actions.sensors')
            ->when($search, function ($q) use ($search) {
                $q->where('description_alarm', 'like', "%{$search}%");
            })
            // pakai created_at biar urutan sesuai waktu dibuat
            ->orderBy('created_at', $sort)
            ->paginate(10)
            ->withQueryString();
    
        return view('alarms.index', compact('alarms', 'search', 'sort'));
    }
    

    public function create()
    {
        return view('alarms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description_alarm' => 'required|string|max:255',
            'step'              => 'nullable|integer',
            'actions'           => 'required|array',
            'actions.*.action_text' => 'required|string',
            'actions.*.sensors' => 'array',
            'actions.*.sensors.*.sensor_name' => 'required|string',
            'actions.*.sensors.*.komponen'    => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'actions.*.sensors.*.plc_io'      => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);

        // buat alarm
        $alarm = Alarm::create([
            'description_alarm' => $validated['description_alarm'],
            'step'              => $validated['step'],
        ]);

        // simpan actions + sensors
        foreach ($validated['actions'] as $actionData) {
            $action = $alarm->actions()->create([
                'action_text' => $actionData['action_text'],
            ]);

            if (!empty($actionData['sensors'])) {
                foreach ($actionData['sensors'] as $sensorData) {
                    $komponenPath = $sensorData['komponen']->store('komponen', 'public');
                    $plcIoPath    = $sensorData['plc_io']->store('plc_io', 'public');

                    $action->sensors()->create([
                        'sensor_name' => $sensorData['sensor_name'],
                        'komponen'    => $komponenPath,
                        'plc_io'      => $plcIoPath,
                    ]);
                }
            }
        }

        return redirect()->route('alarms.index')->with('success', 'Data alarm ditambahkan.');
    }

    public function edit(Alarm $alarm)
    {
        $alarm->load('actions.sensors');
        return view('alarms.edit', compact('alarm'));
    }

    public function update(Request $request, Alarm $alarm)
    {
        $validated = $request->validate([
            'description_alarm' => 'required|string|max:255',
            'step'              => 'nullable|integer',
            'actions'           => 'required|array',
            'actions.*.action_text' => 'required|string',
            'actions.*.sensors' => 'array',
            'actions.*.sensors.*.sensor_name' => 'required|string',
            'actions.*.sensors.*.komponen'    => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'actions.*.sensors.*.plc_io'      => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);
    
        $alarm->update([
            'description_alarm' => $validated['description_alarm'],
            'step'              => $validated['step'] ?? null,
        ]);
    
        // hapus dulu action lama (opsi sederhana)
        $alarm->actions()->delete();
    
        foreach ($validated['actions'] as $i => $actionData) {
            $action = $alarm->actions()->create([
                'action_text' => $actionData['action_text'],
            ]);
    
            if (!empty($actionData['sensors'])) {
                foreach ($actionData['sensors'] as $j => $sensorData) {
                    // ambil file baru kalau ada
                    if (!empty($sensorData['komponen'])) {
                        $komponenPath = $sensorData['komponen']->store('komponen', 'public');
                    } else {
                        $komponenPath = $request->input("actions.$i.sensors.$j.komponen_old"); // pakai lama
                    }
    
                    if (!empty($sensorData['plc_io'])) {
                        $plcIoPath = $sensorData['plc_io']->store('plc_io', 'public');
                    } else {
                        $plcIoPath = $request->input("actions.$i.sensors.$j.plc_io_old");
                    }
    
                    $action->sensors()->create([
                        'sensor_name' => $sensorData['sensor_name'],
                        'komponen'    => $komponenPath,
                        'plc_io'      => $plcIoPath,
                    ]);
                }
            }
        }
    
        return redirect()->route('alarms.index')->with('success','Data alarm diperbarui.');
    }
    


    public function destroy(Alarm $alarm)
    {
        // hapus semua action + sensor otomatis karena foreign key cascade
        $alarm->delete();
        return redirect()->route('alarms.index')->with('success', 'Data alarm dihapus.');
    }
}
