<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\BuildingUnitController;
use App\Http\Controllers\BuildingAmenityController;
use App\Http\Controllers\BuildingPolicyController;
use App\Http\Controllers\PublicUnitController;
use App\Http\Controllers\Auth\RegisteredUserController;

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
Route::get('/login', [LoginController::class, 'index'])->name('login');

Route::post('/login', [LoginController::class, 'create']);

Route::post('/register', [LoginController::class, 'store']);

Route::get('/units', [PublicUnitController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/user/update/owner', UserController::class, 'updateOwner');

    //
    // BUILDINGS
    //

    Route::get('/buildings', [BuildingController::class, 'index']);

    Route::get('/building/show/{id}', [BuildingController::class, 'show']);

    Route::post('/building/create', [BuildingController::class, 'store']);

    Route::post('/building/update', [BuildingController::class, 'update']);

    Route::post('/building/delete', [BuildingController::class, 'destroy']);

    //
    // BUILDINGS UNIT
    //

    Route::get('/building-units', [BuildingUnitController::class, 'index']);

    Route::get('/building-unit/show/{id}', [BuildingUnitController::class, 'show']);

    Route::post('/building-unit/create', [BuildingUnitController::class, 'store']);

    Route::post('/building-unit/update', [BuildingUnitController::class, 'update']);

    Route::post('/building-unit/delete', [BuildingUnitController::class, 'destroy']);

    //
    // BUILDINGS AMENITY
    //

    Route::get('/building-amenities', [BuildingAmenityController::class, 'index']);

    Route::get('/building-amenity/show/{id}', [BuildingAmenityController::class, 'show']);

    Route::post('/building-amenity/create', [BuildingAmenityController::class, 'store']);

    Route::post('/building-amenity/update', [BuildingAmenityController::class, 'update']);

    Route::post('/building-amenity/delete', [BuildingAmenityController::class, 'destroy']);

    //
    // BUILDINGS POLICY
    //

    Route::get('/building-policies', [BuildingPolicyController::class, 'index']);

    Route::get('/building-policy/show/{id}', [BuildingPolicyController::class, 'show']);

    Route::post('/building-policy/create', [BuildingPolicyController::class, 'store']);

    Route::post('/building-policy/update', [BuildingPolicyController::class, 'update']);

    Route::post('/building-policy/delete', [BuildingPolicyController::class, 'destroy']);

    //
    // AUTH LOGOUT
    //
    Route::post('/logout', [LoginController::class, 'destroy']);
});
