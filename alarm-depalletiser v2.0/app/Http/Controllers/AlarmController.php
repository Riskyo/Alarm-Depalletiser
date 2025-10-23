<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Action;
use App\Models\Sensor;
use App\Models\Visitor;
use App\Models\SearchLog; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class AlarmController extends Controller
{
    public function index(Request $request)
{
    $search = trim((string)$request->input('search'));
    $sort   = $request->input('sort', 'asc');
    $ip     = $request->ip();

    // ✅ Simpan IP unik ke tabel visitors
    if (!\App\Models\Visitor::where('ip_address', $ip)->exists()) {
        \App\Models\Visitor::create(['ip_address' => $ip]);
    }

    // ✅ Hitung total visitor unik
    $visitorCount = \App\Models\Visitor::count();

    // Ambil data alarm
    $alarms = \App\Models\Alarm::with('actions.sensors')
        ->when($search, function ($q) use ($search) {
            $q->where('description_alarm', 'like', "%{$search}%");
        })
        ->orderBy('created_at', $sort)
        ->paginate(10)
        ->withQueryString();

    // ✅ Simpan ke search log hanya jika hasil ditemukan
    if ($search !== '' && $alarms->total() > 0) {
        \App\Models\SearchLog::create([
            'query'      => $search,
            'ip_address' => $ip,
        ]);
    }

    // ✅ Ambil 5 pencarian paling sering (untuk admin)
    $mostSearched = \App\Models\SearchLog::select('query')
        ->selectRaw('COUNT(*) as total')
        ->groupBy('query')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

    return view('alarms.index', compact('alarms', 'search', 'sort', 'visitorCount', 'mostSearched'));
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

        $alarm = Alarm::create([
            'description_alarm' => $validated['description_alarm'],
            'step'              => $validated['step'],
        ]);

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

        $alarm->actions()->delete();

        foreach ($validated['actions'] as $i => $actionData) {
            $action = $alarm->actions()->create([
                'action_text' => $actionData['action_text'],
            ]);

            if (!empty($actionData['sensors'])) {
                foreach ($actionData['sensors'] as $j => $sensorData) {
                    if (!empty($sensorData['komponen'])) {
                        $komponenPath = $sensorData['komponen']->store('komponen', 'public');
                    } else {
                        $komponenPath = $request->input("actions.$i.sensors.$j.komponen_old");
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
        $alarm->delete();
        return redirect()->route('alarms.index')->with('success', 'Data alarm dihapus.');
    }
}
