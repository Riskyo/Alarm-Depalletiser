@extends('layouts.app')

@section('content')
@auth
    @can('isAdmin')
        <div class="bg-gray-800 text-white p-3 rounded mb-4 text-center">
            👥 Total Pengunjung: <span class="font-bold">{{ $visitorCount }}</span>
        </div>
    @endcan
@endauth

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Data Alarm Depalletiser</h1>

    <div class="p-3 bg-gray-100 rounded">
        <h4 class="font-semibold mb-2 flex items-center">
            🔍 Most Searched:
            <span class="ml-2 flex flex-wrap gap-2">
                @forelse ($mostSearched as $s)
                    <a href="{{ url()->current() }}?search={{ urlencode($s->query) }}"
                       class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-sm px-2 py-1 rounded transition">
                        {{ $s->query }} <span class="text-gray-500">({{ $s->total }})</span>
                    </a>
                @empty
                    <span class="text-gray-500 text-sm">Belum ada data pencarian</span>
                @endforelse
            </span>
        </h4>
    </div>
</div>


    @if(session('success'))
        <div class="bg-green-100 text-green-800 rounded p-3 mb-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- 🔍 Search -->
    <form method="GET" action="{{ route('alarms.index') }}" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ $search ?? '' }}" 
               placeholder="Cari description..."
               class="border rounded px-3 py-2 flex-1">

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
        @auth
            @can('isAdmin')
                <a href="{{ route('alarms.create') }}" 
                   class="bg-emerald-600 text-white px-4 py-2 rounded">Tambah</a>
            @endcan
        @endauth
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border text-center w-12">No</th>
                    <th class="p-2 border">Description Alarm</th>
                    <th class="p-2 border text-center">Step</th>
                    <th class="p-2 border">Action</th>
                    <th class="p-2 border">Sensor</th>
                    <th class="p-2 border">Komponen</th>
                    <th class="p-2 border">PLC I/O</th>
                    @can('isAdmin')<th class="p-2 border text-center">Aksi</th>@endcan
                </tr>
            </thead>
            <tbody>
                @forelse($alarms as $index => $alarm)
                    @php
                        // hitung total baris yang akan digabung
                        $rows = 0;
                        foreach($alarm->actions as $action){
                            $rows += max($action->sensors->count(),1);
                        }
                        $rowspan = $rows ?: 1;
                        $firstRow = true;
                    @endphp

                    @if($alarm->actions->isEmpty())
                        <tr>
                            <td class="p-2 border text-center">
                                {{ $alarms->firstItem() + $index }}
                            </td>
                            <td class="p-2 border">{{ $alarm->description_alarm }}</td>
                            <td class="p-2 border text-center">{{ $alarm->step }}</td>
                            <td class="p-2 border text-gray-400" colspan="4">Belum ada action</td>
                            @can('isAdmin')
                                <td class="p-2 border text-center">
                                    <a href="{{ route('alarms.edit', $alarm) }}" class="text-blue-700 underline">Edit</a>
                                    <form action="{{ route('alarms.destroy', $alarm) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-700 underline ml-2">Hapus</button>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @else
                        @foreach($alarm->actions as $aIndex => $action)
                            @php $sensorCount = max($action->sensors->count(),1); @endphp

                            @for($sIndex=0; $sIndex<$sensorCount; $sIndex++)
                                <tr class="align-top">
                                    @if($firstRow)
                                        <td class="p-2 border text-center" rowspan="{{ $rowspan }}">
                                            {{ $alarms->firstItem() + $index }}
                                        </td>
                                        <td class="p-2 border" rowspan="{{ $rowspan }}">
                                            {{ $alarm->description_alarm }}
                                        </td>
                                        <td class="p-2 border text-center" rowspan="{{ $rowspan }}">
                                            {{ $alarm->step }}
                                        </td>
                                        @php $firstRow=false; @endphp
                                    @endif

                                    <!-- Action -->
                                    @if($sIndex===0)
                                        <td class="p-2 border" rowspan="{{ $sensorCount }}">
                                            {{ $action->action_text }}
                                        </td>
                                    @endif

                                    <!-- Sensor -->
                                    <td class="p-2 border">
                                        {{ $action->sensors[$sIndex]->sensor_name ?? '-' }}
                                    </td>

                                    <!-- Komponen -->
                                    <td class="p-2 border text-center">
                                        @if(isset($action->sensors[$sIndex]) && $action->sensors[$sIndex]->komponen)
                                            <a href="{{ asset('storage/'.$action->sensors[$sIndex]->komponen) }}" target="_blank">
                                                <img src="{{ asset('storage/'.$action->sensors[$sIndex]->komponen) }}"
                                                     class="h-16 w-16 object-cover border rounded mx-auto">
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <!-- PLC I/O -->
                                    <td class="p-2 border text-center">
                                        @if(isset($action->sensors[$sIndex]) && $action->sensors[$sIndex]->plc_io)
                                            <a href="{{ asset('storage/'.$action->sensors[$sIndex]->plc_io) }}" target="_blank">
                                                <img src="{{ asset('storage/'.$action->sensors[$sIndex]->plc_io) }}"
                                                     class="h-16 w-16 object-cover border rounded mx-auto">
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    @if($sIndex===0 && $aIndex===0)
                                        @can('isAdmin')
                                            <td class="p-2 border text-center" rowspan="{{ $rowspan }}">
                                                <a href="{{ route('alarms.edit', $alarm) }}" class="text-blue-700 underline">Edit</a>
                                                <form action="{{ route('alarms.destroy', $alarm) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-700 underline ml-2">Hapus</button>
                                                </form>
                                            </td>
                                        @endcan
                                    @endif
                                </tr>
                            @endfor
                        @endforeach
                    @endif
                @empty
                    <tr>
                        <td class="p-3 border text-center" colspan="8">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $alarms->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
window.addEventListener('load', function() {
    if (typeof Shepherd === 'undefined') {
        console.error('Shepherd belum siap!');
        return;
    }

    const TOUR_KEY = 'app_seen_tour_v4';
    const forceShow = false;
    if (!forceShow && localStorage.getItem(TOUR_KEY)) return;

    const tour = new Shepherd.Tour({
        defaultStepOptions: {
            cancelIcon: { enabled: true },
            classes: 'shadow-md bg-purple-600 text-white rounded-md',
            scrollTo: { behavior: 'smooth', block: 'center' }
        },
        useModalOverlay: true
    });

    const addStepIf = (selectorOrEl, opts) => {
        let el = null;

        // Jika input berupa string selector
        if (typeof selectorOrEl === 'string') {
            el = document.querySelector(selectorOrEl);
        } else {
            // Jika langsung elemen
            el = selectorOrEl;
        }

        if (el) {
            opts.attachTo.element = el;
            tour.addStep(opts);
        }
    };

    // ============ UNTUK SEMUA USER ============
    addStepIf('h1', {
        title: 'Selamat Datang 👋',
        text: 'Ini adalah halaman utama Alarm Depalletiser.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: tour.next }]
    });

    addStepIf('input[name=search]', {
        title: 'Bagian Pencarian 🔍',
        text: 'Gunakan kolom ini untuk mencari alarm berdasarkan deskripsi atau keyword.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: tour.next }]
    });

    addStepIf('button.bg-blue-600', {
        title: 'Tombol Search',
        text: 'Klik tombol ini untuk menjalankan pencarian.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: tour.next }]
    });

    // === PERBAIKAN: cari kolom PLC I/O secara manual ===
    const thElements = document.querySelectorAll('th');
    let plcTh = null;
    thElements.forEach(th => {
        if (th.textContent.trim().includes('PLC I/O')) {
            plcTh = th;
        }
    });

    addStepIf(plcTh, {
        title: 'Kolom PLC I/O ⚙️',
        text: 'Klik gambar di kolom ini untuk melihat detail PLC I/O dalam ukuran penuh.',
        attachTo: { on: 'top' },
        buttons: [{ text: 'Lanjut', action: tour.next }]
    });

    // ============ KHUSUS ADMIN ============
    @can('isAdmin')
    addStepIf('a.bg-emerald-600', {
        title: 'Tambah Data ➕',
        text: 'Klik tombol ini untuk menambah data alarm baru.',
        attachTo: { on: 'left' },
        buttons: [{ text: 'Lanjut', action: tour.next }]
    });

    addStepIf('a.text-blue-700', {
        title: 'Edit Data ✏️',
        text: 'Gunakan tombol Edit untuk mengubah informasi alarm yang sudah ada.',
        attachTo: { on: 'top' },
        buttons: [{ text: 'Lanjut', action: tour.next }]
    });

    addStepIf('button.text-red-700', {
        title: 'Hapus Data ❌',
        text: 'Klik tombol Hapus untuk menghapus data alarm. Hati-hati, data akan terhapus permanen.',
        attachTo: { on: 'top' },
        buttons: [{ text: 'Selesai', action: tour.complete }]
    });
    @else
    addStepIf('table.min-w-full', {
        title: 'Hasil Alarm 📋',
        text: 'Di sini kamu bisa melihat hasil pencarian dan detail setiap alarm.',
        attachTo: { on: 'top' },
        buttons: [{ text: 'Selesai', action: tour.complete }]
    });
    @endcan

    // jalankan tour
    if (tour.steps.length) {
        setTimeout(() => {
            tour.start();
            localStorage.setItem(TOUR_KEY, '1');
        }, 500);
    }

    tour.on('complete', () => localStorage.setItem(TOUR_KEY, '1'));
    tour.on('cancel', () => localStorage.setItem(TOUR_KEY, '1'));
});
</script>
@endsection

