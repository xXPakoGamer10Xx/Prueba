<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Ginecologia\MaterialController;
use App\Http\Controllers\Ginecologia\PacienteController;
use App\Http\Controllers\Servicios\MantenimientoController;
use App\Http\Controllers\Servicios\EncargadoMantenimientoController;
use App\Http\Controllers\Servicios\BajaController;
use App\Http\Controllers\Ginecologia\CirugiaPageController;
use App\Http\Controllers\Ginecologia\CirugiaGeneralController;
use App\Http\Controllers\Ginecologia\CirugiaGinecologicaController;
use App\Http\Controllers\Ginecologia\ReporteController;
use App\Http\Controllers\Servicios\InventarioController;

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
        return view('odontologia.consultorio');
    })->name('odontologia.consultorio.index');

    Route::get('/odontologia/consultorio/materiales', function () {
        return view('odontologia.materiales');
    })->name('odontologia.consultorio.materiales');

    Route::get('/odontologia/consultorio/almacen', function () {
        return view('odontologia.almacen');
    })->name('odontologia.consultorio.almacen');

    Route::get('/odontologia/consultorio/insumos', function () {
        return view('odontologia.insumos');
    })->name('odontologia.consultorio.insumos');

    Route::get('/odontologia/consultorio/peticiones', function () {
        return view('odontologia.peticiones');
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
        return view('odontologia.consultorio');
    })->name('odontologia.almacen.consultorio');

    Route::get('/odontologia/almacen/', function () {
        return view('odontologia.almacen');
    })->name('odontologia.almacen.index');

    Route::get('/odontologia/almacen/insumos', function () {
        return view('odontologia.insumos');
    })->name('odontologia.almacen.insumos');

    Route::get('/odontologia/almacen/peticiones', function () {
        return view('odontologia.peticiones');
    })->name('odontologia.almacen.peticiones');

    Route::get('/odontologia/almacen/lyp', function () {
        return view('odontologia.lyp');
    })->name('odontologia.almacen.lyp');
});
// --- FIN DEL GRUPO PROTEGIDO ---

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

// --- GRUPO DE RUTAS PROTEGIDAS PARA 'Ginecologia' ---
// Solo usuarios autenticados con el rol 'encargado_ginecologia' podrán acceder.
Route::middleware(['auth', 'role:encargado_ginecologia'])->group(function () {
    Route::get('/ginecologia', function () { return view('ginecologia.index'); })->name('ginecologia.index');
    Route::get('/material', function () { return view('ginecologia.material'); })->name('ginecologia.material');
    Route::get('/expediente', function () { return view('ginecologia.expediente'); })->name('ginecologia.expediente');
    Route::get('/reporte', function () { return view('ginecologia.reporte'); })->name('ginecologia.reporte');
    Route::get('/cirugia', function () { return view('ginecologia.cirugia'); })->name('ginecologia.cirugia');
});

Route::resource('ginecologia/material', MaterialController::class)
     ->parameters(['material' => 'material'])
     ->names('material'); 

     // RUTA PARA MOSTRAR LA PÁGINA PRINCIPAL CON LAS DOS TABLAS
    Route::get('/cirugia', [CirugiaPageController::class, 'index'])->name('cirugia.index');

    // RUTAS PARA EL CRUD DE CIRUGÍAS GENERALES
    Route::resource('cirugiageneral', CirugiaGeneralController::class)->except(['index', 'show', 'create', 'edit']);

    // RUTAS PARA EL CRUD DE CIRUGÍAS GINECOLÓGICAS
    Route::resource('cirugiaginecologica', CirugiaGinecologicaController::class)->except(['index', 'show', 'create', 'edit']);
Route::resource('expediente', PacienteController::class);


// --- INICIAN RUTAS DE SERVICIOS ---

// Rutas para Mantenimiento

Route::middleware(['auth', 'role:encargado_servicios'])->prefix('servicios')->name('servicios.')->group(function () {
    Route::view('/', 'servicios.index')->name('index');
    Route::view('/areas', 'servicios.areas')->name('areas');
    Route::view('/inventario', 'servicios.inventario')->name('inventario');
    Route::view('/mantenimiento', 'servicios.mantenimiento')->name('mantenimiento');
    Route::view('/bajas', 'servicios.bajas')->name('bajas');
});

// --- TERMINAN RUTAS DE SERVICIOS ---


Route::get('/reporte', [ReporteController::class, 'index'])->name('reporte.index');

Route::resource('material', MaterialController::class)
    ->middleware(['auth', 'role:encargado_ginecologia']) // La protegemos aquí
    ->parameters(['material' => 'material']); // El nombre del parámetro es 'material'

    //Fin de grupo Ginecologia
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});








require __DIR__.'/auth.php';
