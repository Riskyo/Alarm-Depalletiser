@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-4xl">
    <h1 class="text-2xl font-semibold mb-4">Tambah Data Alarm</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 rounded p-3 mb-3">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('alarms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
            <label class="block mb-1 font-medium">Deskripsi Alarm</label>
            <input type="text" name="description_alarm" value="{{ old('description_alarm') }}"
                   class="border rounded w-full px-3 py-2">
        </div>

        <div>
            <label class="block mb-1 font-medium">Step</label>
            <input type="number" name="step" value="{{ old('step') }}"
                   class="border rounded w-full px-3 py-2">
        </div>

        <hr class="my-4">
        <h3 class="text-xl font-semibold mb-2">Actions</h3>
        <div id="actions-wrapper" class="space-y-4">
            {{-- Action pertama --}}
            <div class="action-block border p-4 rounded">
                <div class="mb-3">
                    <label class="block mb-1">Teks Aksi</label>
                    <input type="text" name="actions[0][action_text]" class="border rounded w-full px-3 py-2">
                </div>

                <h5 class="text-lg font-medium mb-2">Sensors</h5>
                <div class="sensors-wrapper space-y-3">
                    <div class="sensor-block border p-3 rounded">
                        <div class="mb-2">
                            <label class="block mb-1">Nama Sensor</label>
                            <input type="text" name="actions[0][sensors][0][sensor_name]" class="border rounded w-full px-3 py-2">
                        </div>
                        <div class="mb-2">
                            <label class="block mb-1">Gambar Komponen</label>
                            <input type="file" name="actions[0][sensors][0][komponen]" accept="image/*" class="border rounded w-full px-3 py-2">
                        </div>
                        <div class="mb-2">
                            <label class="block mb-1">Gambar PLC I/O</label>
                            <input type="file" name="actions[0][sensors][0][plc_io]" accept="image/*" class="border rounded w-full px-3 py-2">
                        </div>
                    </div>
                </div>
                <button type="button" class="mt-2 bg-gray-200 hover:bg-gray-300 text-sm px-3 py-1 rounded add-sensor-btn">+ Tambah Sensor</button>
            </div>
        </div>

        <button type="button" id="add-action" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">+ Tambah Action</button>

        <div class="flex gap-2 mt-4">
            <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('alarms.index') }}" class="px-4 py-2 border rounded">Batal</a>
        </div>
    </form>
</div>

<script>
let actionIndex = 1;

document.getElementById('add-action').addEventListener('click', function() {
    const wrapper = document.getElementById('actions-wrapper');
    const html = `
    <div class="action-block border p-4 rounded mt-3">
        <div class="mb-3">
            <label class="block mb-1">Teks Aksi</label>
            <input type="text" name="actions[${actionIndex}][action_text]" class="border rounded w-full px-3 py-2">
        </div>

        <h5 class="text-lg font-medium mb-2">Sensors</h5>
        <div class="sensors-wrapper space-y-3">
            <div class="sensor-block border p-3 rounded">
                <div class="mb-2">
                    <label class="block mb-1">Nama Sensor</label>
                    <input type="text" name="actions[${actionIndex}][sensors][0][sensor_name]" class="border rounded w-full px-3 py-2">
                </div>
                <div class="mb-2">
                    <label class="block mb-1">Gambar Komponen</label>
                    <input type="file" name="actions[${actionIndex}][sensors][0][komponen]" accept="image/*" class="border rounded w-full px-3 py-2">
                </div>
                <div class="mb-2">
                    <label class="block mb-1">Gambar PLC I/O</label>
                    <input type="file" name="actions[${actionIndex}][sensors][0][plc_io]" accept="image/*" class="border rounded w-full px-3 py-2">
                </div>
            </div>
        </div>
        <button type="button" class="mt-2 bg-gray-200 hover:bg-gray-300 text-sm px-3 py-1 rounded add-sensor-btn">+ Tambah Sensor</button>
    </div>
    `;
    wrapper.insertAdjacentHTML('beforeend', html);
    actionIndex++;
});

// Delegate event add sensor
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('add-sensor-btn')) {
        const actionBlock = e.target.closest('.action-block');
        const sensorsWrapper = actionBlock.querySelector('.sensors-wrapper');

        // ambil index action
        const actionInput = actionBlock.querySelector('input[name^="actions"]');
        const actionIdxMatch = actionInput.name.match(/actions\[(\d+)\]/);
        const actionIdx = actionIdxMatch ? actionIdxMatch[1] : 0;

        const sensorCount = sensorsWrapper.querySelectorAll('.sensor-block').length;

        const sensorHtml = `
        <div class="sensor-block border p-3 rounded">
            <div class="mb-2">
                <label class="block mb-1">Nama Sensor</label>
                <input type="text" name="actions[${actionIdx}][sensors][${sensorCount}][sensor_name]" class="border rounded w-full px-3 py-2">
            </div>
            <div class="mb-2">
                <label class="block mb-1">Gambar Komponen</label>
                <input type="file" name="actions[${actionIdx}][sensors][${sensorCount}][komponen]" accept="image/*" class="border rounded w-full px-3 py-2">
            </div>
            <div class="mb-2">
                <label class="block mb-1">Gambar PLC I/O</label>
                <input type="file" name="actions[${actionIdx}][sensors][${sensorCount}][plc_io]" accept="image/*" class="border rounded w-full px-3 py-2">
            </div>
        </div>
        `;
        sensorsWrapper.insertAdjacentHTML('beforeend', sensorHtml);
    }
});
</script>
@endsection
