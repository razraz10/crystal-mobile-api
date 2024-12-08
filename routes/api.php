<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InhibitController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RekemAdvancedController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;




Route::controller(AuthController::class)->group(function () {

    Route::get('/user', 'getCurrentUser')->middleware(['verify.cookie', 'auth:api']);
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware(['verify.cookie', 'auth:api']);
});

//Markets table routes.

Route::controller(MarketController::class)
    ->middleware(['verify.cookie', 'auth:api'])
    ->prefix('markets')->group(function () {

        Route::get('/', 'index');
        Route::get('/lastuserupdate', 'getLastUserUpdate');
        Route::get('/byear', 'getMarketsByYear');
        Route::get('/{id}', 'get');
        ///these routes the user must has permission_name='admin'
        Route::post('/', 'store');
        Route::put('/{id?}', 'update');
        Route::put('/updatemonth/{id?}', 'updateMarketMonth');
        Route::delete('/massdelete', 'deleteMarkets');
        Route::delete('/{id?}', 'delete');
    });

//Inhibts table routes.
Route::controller(InhibitController::class)
    ->middleware(['verify.cookie', 'auth:api'])
    ->prefix('inhibits')
    ->group(function () {
        Route::get('/lastuserupdate', 'getLastUserUpdate');
        Route::get('/', 'index');
        Route::get('/byyearandmonth', 'getinhibitByYearAndMonth');
        Route::get('/{id?}', 'get');
        ///these routes the user must has permission_name='admin'
        Route::post('/', 'store');
        Route::put('/{id?}', 'update');
        Route::delete('/massdelete', 'deleteInhibits');
        Route::delete('/{id?}', 'delete');
    });

//Missions table routes.
Route::controller(MissionController::class)
    ->middleware(['verify.cookie', 'auth:api'])
    ->prefix('missions')->group(function () {

        Route::get('/lastuserupdate', 'getLastUserUpdate');
        Route::get('/', 'index');
        Route::get('/byyearandmonth', 'getMissionsByYearAndMonth');
        Route::get('/{id?}', 'get');

        ///these routes the user must has permission_name='admin'
        Route::post('/', 'store');
        Route::put('/{id?}', 'update');
        Route::delete('/massdelete', 'deleteMissions');
        Route::delete('/{id?}', 'delete');
    });

/////////RekemAdvanced routes functions.
//same table as missions table just make sure to hide the fileds ('month','comulative_per_month','plan_week_per_month')
Route::controller(RekemAdvancedController::class)
    ->middleware(['verify.cookie', 'auth:api'])
    ->prefix('rekemadvanced')->group(function () {
        Route::get('/lastuserupdate', 'getLastUserUpdate');
        Route::get('/', 'index');
        Route::get('/byyearandmonth', 'getMissionsByYearAndMonth');
        Route::get('/{id?}', 'get');
    });

//users routes functions.
Route::controller(UserController::class)
    ->middleware(['verify.cookie', 'auth:api'])
    ->prefix('users')->group(function () {
        Route::get('/', 'index');
        Route::get('/{search_string?}', 'searchUser');
        Route::put('/{id?}', 'setNewPermission');
        Route::post('/', 'store');
        Route::delete('/{id?}', 'delete');
    });
//permissions route functions.
Route::controller(PermissionsController::class)
    ->middleware(['verify.cookie', 'auth:api'])
    ->prefix('permissions')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
    });
