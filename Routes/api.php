<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\ConnectionModule\Http\Controllers\API\ConnectionController;

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

Route::group(['middleware' => ['api','auth','isVerified','isEmailVerified','IsProfileCompleted']], function () {
    Route::get('user-connections', [ConnectionController::class,'getUserConnections'] );
    //Route::get('user-connections-pending', 'ConnectionController@getUserConnectionsPending');
    Route::post('create-connection', [ConnectionController::class,'store']);
    Route::get('delete-connection/{id}',[ConnectionController::class,'destroy']);
    Route::get('accept-connection/{id}', [ConnectionController::class,'connectionAccepted']);
    Route::get('reject-connection/{id}', [ConnectionController::class,'connectionRejected']);
    Route::get('search-connection/{full_name}', [ConnectionController::class,'search']);
});

