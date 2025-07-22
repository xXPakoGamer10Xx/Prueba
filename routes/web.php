<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- GRUPO DE RUTAS PROTEGIDAS PARA 'encargado_servicios' ---
// Solo usuarios autenticados con el rol 'encargado_servicios' podrán acceder.
Route::middleware(['auth', 'role:encargado_servicios'])->group(function () {
    Route::get('/servicios', function () {
        return view('servicios.index');
    })->name('servicios.index');

    Route::get('/inventario', function () {
        return view('servicios.inventario');
    })->name('servicios.inventario');

    Route::get('/mantenimiento', function () {
        return view('servicios.mantenimiento');
    })->name('servicios.mantenimiento');

    Route::get('/bajas', function () {
        return view('servicios.bajas');
    })->name('servicios.bajas');

    Route::get('/areas', function () {
        return view('servicios.areas');
    })->name('servicios.areas');

    Route::get('/encargados', function () {
        return view('servicios.encargados');
    })->name('servicios.encargados');
});
// --- FIN DEL GRUPO PROTEGIDO ---

Route::middleware(['auth', 'role:encargado_ginecologia'])->group(function () {
    Route::get('/ginecologia', function () {
        return view('ginecologia.index');
    })->name('ginecologia.index');

    Route::get('/material', function () {
        return view('ginecologia.material');
    })->name('ginecologia.material');

    Route::get('/expediente', function () {
        return view('ginecologia.expediente');
    })->name('ginecologia.expediente');

    Route::get('/reporte', function () {
        return view('ginecologia.reporte');
    })->name('ginecologia.reporte');

    Route::get('/cirugia', function () {
        return view('ginecologia.cirugia');
    })->name('ginecologia.cirugia');

});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('odontologia/consultorio', 'odontologia.consultorio')->name('odontologia.consultorio');
});

require __DIR__.'/auth.php';
