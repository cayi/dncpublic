<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register'=>false,
    'reset'=>false,
    'verify'=>false,
    'confirm'=>false
    ]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Route::get('/evaluadores/pdf', 'App\Http\Controllers\EvaluadoresController@imprime');
//Route::post('/eva/grabar', 'App\Http\Controllers\EvaluadosController@grabar');
//Route::get('/eva/{id}', 'App\Http\Controllers\EvaluadosController@eva');
//Route::get('/eva2', 'App\Http\Controllers\EvaluadoresController@eva2');
//Route::get('/val/{id}', 'App\Http\Controllers\EvaluadosController@val');
//Route::get('/evaluadores', 'App\Http\Controllers\EvaluadoresController@index');
//Route::get('/imp/{id}', 'App\Http\Controllers\EvaluadosController@imp');
//Route::get('/imp2/{id}', 'App\Http\Controllers\EvaluadosController@imp2');
    
Route::prefix('admin')->group(function () {   

    Route::get('/exp/{action}', 'App\Http\Controllers\EvaluadosController@exp');

    Route::get('/import_excel', 'App\Http\Controllers\admin\ImportExcelController@index');
    Route::post('/import_excel/import', 'App\Http\Controllers\admin\ImportExcelController@import');
    Route::resource('/Periodos', App\Http\Controllers\admin\PeriodosController::class);
    Route::resource('/Perfilusers', App\Http\Controllers\admin\PerfilusersController::class);
    Route::resource('/Usuarios', App\Http\Controllers\admin\UsuariosController::class);
    Route::resource('/Dncs', App\Http\Controllers\admin\DncsController::class);
    Route::resource('/Plantillas', App\Http\Controllers\admin\PlantillasController::class);
});