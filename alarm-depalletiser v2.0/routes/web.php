<?php

use App\Http\Controllers\AlarmController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\SensorController;
use Illuminate\Support\Facades\Route;

// === Publik ===
Route::get('/', [AlarmController::class, 'index'])->name('alarms.index');
// Route::get('/alarms/{alarm}', [AlarmController::class, 'show'])->name('alarms.show'); // ← hapus / komentari ini

require __DIR__.'/auth.php';

// === Admin ===
Route::middleware(['auth','can:isAdmin'])->group(function(){
    // CRUD Alarm kecuali index & show
    Route::resource('alarms', AlarmController::class)->except(['index', 'show']);

    // nested action & sensor
    Route::post('alarms/{alarm}/actions', [ActionController::class,'store'])->name('actions.store');
    Route::post('actions/{action}/sensors', [SensorController::class,'store'])->name('sensors.store');
});
