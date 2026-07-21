<?php

use App\Http\Controllers\ControllerAreas;
use App\Http\Controllers\ControllerEmpleados;
use App\Http\Controllers\ControllerUsuarios;
use App\Http\Controllers\ControllerInicio;
use App\Http\Controllers\ControllerMateriales;
use App\Http\Controllers\ControllerMovimientos;
use App\Http\Controllers\ControllerOrdenes;
use App\Http\Controllers\ControllerTiempoextra;
use App\Http\Controllers\ControllerTiemposmuertos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´//
//Ordenes publicas
//================================================================//
//Ordenes
Route::get('/Ordenes', [ControllerOrdenes::class, 'index'])->name('Ordenes');
//================================================================//
//Ordenes
Route::get('/Tiempoextra', [ControllerTiempoextra::class, 'index'])->name('Tiempoextra');
//================================================================//
//Home
Route::get('/', [ControllerInicio::class, 'index'])->name('Home');
//--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´//

//--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´//
//inicio de sesion publico
Route::get('/login', function () {
  return view('Containers\ConLogin');
})->name('login');
//--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´//


//--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´//
//Rutas protegidas
Route::middleware(['auth'])->group(function () {
  //================================================================//
  //Areas
  Route::get('/Areas', [ControllerAreas::class, 'index'])->name('Areas');
  //================================================================//
  //Empleados
  Route::get('/Usuarios', [ControllerUsuarios::class, 'index'])->name('Usuarios');
  //================================================================//
  //Empleados
  Route::get('/Empleados', [ControllerEmpleados::class, 'index'])->name('Empleados');
  //================================================================//
  //Materiales
  Route::get('/Materiales', [ControllerMateriales::class, 'index'])->name('Materiales');
  //================================================================//
  //Movimientos
  Route::get('/Movimientos', [ControllerMovimientos::class, 'index'])->name('Movimientos');
  //================================================================//
  //Tiempos Muertos
  Route::get('/Tiemposmuertos', [ControllerTiemposmuertos::class, 'index'])->name('Tiemposmuertos');
  //================================================================//
  //Ruta para el cierre de sesion
  Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('Home');
  })->middleware('auth')->name('logout');
});
//--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´--´\´//