<?php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\FichaController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* crearadmin */
Route::post('registrar', [UserController::class,'store'])->name('registrar');
/* login */
Route::post('login', [UserController::class,'show'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
/* CRUD instructores */
Route::get('instructores', [UserController::class,'showInstructors'])->name('instructores');
Route::middleware('auth:sanctum')->put('editar-instru/{id}', [UserController::class, 'update'])->name('editar-instru');
Route::middleware('auth:sanctum')->delete('eliminar-instru/{id}', [UserController::class, 'destroy'])->name('eliminar-instru');
/* CRUD Fichas */
Route::middleware('auth:sanctum')->post('crear-ficha', [FichaController::class,'store'])->name('crear-ficha');
Route::middleware('auth:sanctum')->get('fichas', [FichaController::class,'index'])->name('fichas');
Route::middleware('auth:sanctum')->post('asignar-ficha-instructor', [FichaController::class, 'asignarFichaInstructor'])->name('asignar-ficha-instructor');
/* CRUD aprendices */
Route::middleware('auth:sanctum')->post('crear-aprendiz', [UserController::class,'registrarAprendiz'])->name('crear-aprendiz');
Route::middleware('auth:sanctum')->post('actualizar-perfil', [UserController::class, 'actualizarPerfil'])->name('actualizar-perfil');
Route::middleware('auth:sanctum')->put('cambiar-contraseña/{id}', [UserController::class, 'cambiarContrasena'])->name('usuarios.cambiar-contrasena');
/* CRUD asistencia */
Route::middleware('auth:sanctum')->get('aprendices/{ficha_id}', [AsistenciaController::class, 'show']);
Route::middleware('auth:sanctum')->post('registrar-asistencia', [AsistenciaController::class, 'create'])->name('registrar-asistencia');
/* CRUD actividades */
Route::middleware('auth:sanctum')->post('crear-actividad', [App\Http\Controllers\ActividadController::class, 'store'])->name('crear-actividad');
Route::middleware('auth:sanctum')->get('aprendices-ficha/{ficha_id}', [ActividadController::class, 'aprendicesPorFicha'])->name('aprendices-ficha');
Route::middleware('auth:sanctum')->get('actividades-ficha/{ficha_id}', [ActividadController::class, 'actividadesPorFicha'])->name('actividades-ficha');
Route::middleware('auth:sanctum')->get('actividades-aprendiz', [ActividadController::class, 'actividadesAsignadasAprendiz'])->name('actividades-aprendiz');
