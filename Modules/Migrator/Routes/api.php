<?php

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

Route::middleware('auth:api')->get('/migrator', function (Request $request) {
    return $request->user();
});
Route::prefix('migrator')->group(function() {
    Route::get('/export', 'MigratorController@export')->name('api.migrator.export');
    Route::post('/import', 'MigratorController@import')->name('api.migrator.import');
});
