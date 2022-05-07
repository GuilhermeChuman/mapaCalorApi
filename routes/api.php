<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BairrosController;
use App\Http\Controllers\CidadesController;
use App\Http\Controllers\CasosController;


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

//Bairros
Route::get('bairros', 'App\Http\Controllers\BairrosController@index');
Route::get('bairros/GeoJson', 'App\Http\Controllers\BairrosController@indexGeoJson');
Route::post('bairros/import', 'App\Http\Controllers\BairrosController@import');

//Casos
Route::get('casos', 'App\Http\Controllers\CasosController@index');
Route::get('casosByBairro', 'App\Http\Controllers\CasosController@casosByBairro');
Route::get('casosNoMes', 'App\Http\Controllers\CasosController@casosNoMesGeoJson');
Route::get('casosByBairro/GeoJson', 'App\Http\Controllers\CasosController@casosByBairroGeoJson');
Route::post('importCasos', 'App\Http\Controllers\CasosController@importCasos');

//Cidades
Route::get('cidades', 'App\Http\Controllers\CidadesController@index');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
