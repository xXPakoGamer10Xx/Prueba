<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- GRUPO DE RUTAS PROTEGIDAS PARA 'odontologia_consultorio' ---
Route::middleware(['auth', 'role:odontologia_consultorio'])->group(function () {
    Route::get('/odontologia/consultorio', function () { return view('odontologia.consultorio.index'); })->name('odontologia.consultorio.index');
    Route::get('/odontologia/materiales', function () { return view('odontologia.consultorio.materiales'); })->name('odontologia.consultorio.materiales');
    Route::get('/odontologia/almacen', function () { return view('odontologia.consultorio.almacen'); })->name('odontologia.consultorio.almacen');
    Route::get('/odontologia/insumos', function () { return view('odontologia.consultorio.insumos'); })->name('odontologia.consultorio.insumos');
    Route::get('/odontologia/peticiones', function () { return view('odontologia.consultorio.peticiones'); })->name('odontologia.consultorio.peticiones');
    Route::get('/odontologia/lyp', function () { return view('odontologia.consultorio.lyp'); })->name('odontologia.consultorio.lyp');
});

// --- GRUPO DE RUTAS PROTEGIDAS PARA 'encargado_servicios' ---
Route::middleware(['auth', 'role:encargado_servicios'])->group(function () {
    
    Route::prefix('servicios')->name('servicios.')->group(function () {
        
        
        Route::get('/', function () {
            return view('servicios.index');
        })->name('index');

        Route::get('/areas', function () {
            return view('servicios.areas');
        })->name('areas');

        Route::get('/inventario', function () {
            return view('servicios.inventario');
        })->name('inventario');

        Route::get('/mantenimiento', function () {
            return view('servicios.mantenimiento');
        })->name('mantenimiento');

        Route::get('/bajas', function () {
            return view('servicios.bajas');
        })->name('bajas');

        
    });
});
// --- FIN DEL GRUPO PROTEGIDO ---


Route::middleware(['auth', 'role:encargado_ginecologia'])->group(function () {
    Route::get('/ginecologia', function () { return view('ginecologia.index'); })->name('ginecologia.index');
    Route::get('/material', function () { return view('ginecologia.material'); })->name('ginecologia.material');
    Route::get('/expediente', function () { return view('ginecologia.expediente'); })->name('ginecologia.expediente');
    Route::get('/reporte', function () { return view('ginecologia.reporte'); })->name('ginecologia.reporte');
    Route::get('/cirugia', function () { return view('ginecologia.cirugia'); })->name('ginecologia.cirugia');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
