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
// Solo usuarios autenticados con el rol 'odontologia_consultorio' podrán acceder.
Route::middleware(['auth', 'role:odontologia_consultorio'])->group(function () {
    Route::get('/odontologia/consultorio/', function () {
        return view('odontologia.consultorio.index');
    })->name('odontologia.consultorio.index');

    Route::get('/odontologia/consultorio/materiales', function () {
        return view('odontologia.consultorio.materiales');
    })->name('odontologia.consultorio.materiales');

    Route::get('/odontologia/consultorio/almacen', function () {
        return view('odontologia.consultorio.almacen');
    })->name('odontologia.consultorio.almacen');

    Route::get('/odontologia/consultorio/insumos', function () {
        return view('odontologia.consultorio.insumos');
    })->name('odontologia.consultorio.insumos');

    Route::get('/odontologia/consultorio/peticiones', function () {
        return view('odontologia.consultorio.peticiones');
    })->name('odontologia.consultorio.peticiones');

    Route::get('/odontologia/consultorio/lyp', function () {
        return view('odontologia.lyp');
    })->name('odontologia.consultorio.lyp');
});
// --- FIN DEL GRUPO PROTEGIDO ---

// --- GRUPO DE RUTAS PROTEGIDAS PARA 'odontologia_almacen' ---
// Solo usuarios autenticados con el rol 'odontologia_almacen' podrán acceder.
Route::middleware(['auth', 'role:odontologia_almacen'])->group(function () {
    Route::get('/odontologia/almacen/consultorio', function () {
        return view('odontologia.almacen.consultorio');
    })->name('odontologia.almacen.consultorio');

    Route::get('/odontologia/almacen/', function () {
        return view('odontologia.almacen.index');
    })->name('odontologia.almacen.index');

    Route::get('/odontologia/almacen/insumos', function () {
        return view('odontologia.almacen.insumos');
    })->name('odontologia.almacen.insumos');

    Route::get('/odontologia/almacen/peticiones', function () {
        return view('odontologia.almacen.peticiones');
    })->name('odontologia.almacen.peticiones');

    Route::get('/odontologia/almacen/lyp', function () {
        return view('odontologia.lyp');
    })->name('odontologia.almacen.lyp');
});
// --- FIN DEL GRUPO PROTEGIDO ---

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
});

require __DIR__.'/auth.php';
