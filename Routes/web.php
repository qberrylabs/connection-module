<?php

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


use Illuminate\Support\Facades\Route;
use Modules\ConnectionModule\Http\Controllers\ConnectionController;

Route::group(['middleware' => ['auth','isAdmin']], function() {
    Route::get('/connections',[ConnectionController::class,'getConnections'])->name('admin.connections');
});

Route::group(['middleware' => ['auth','IsRegistrationCompleted','is2fa','IsProfileCompleted']], function() {

    /* Connections */
    Route::post('connection/store', [ConnectionController::class,'create'])->name('connection.store');
    Route::get('connection/change-status/{connectionID}/{status}', [ConnectionController::class,'connectionChangeStatus'])->name('connectionChangeStatus');
    Route::get('connections/{type}',[ConnectionController::class,'getUserConnections'])->name('user.connections');

    // Route::get('connections', [ConnectionController::class,'getUserConnections'])->name('user.connections');
    // Route::get('connections/all', [ConnectionController::class,'getUserAllConnections'])->name('user.all.connections');
});






