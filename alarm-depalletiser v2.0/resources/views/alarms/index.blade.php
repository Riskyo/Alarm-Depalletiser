@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Data Alarm Depalletiser</h1>

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
