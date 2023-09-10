<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\BuildingUnitController;
use App\Http\Controllers\BuildingAmenityController;
use App\Http\Controllers\BuildingPolicyController;
use App\Http\Controllers\PublicUnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResortController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;

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

Route::get('/resorts', [ResortController::class, 'index']);
Route::get('/resorts/show', [ResortController::class, 'indexShow']);

Route::post('/alden/api', function () {
    $result = [];
    foreach(request()->file('file_upload') as $key => $file) {
        $result[] = $file->getClientOriginalName();
    }
    return $result;
});

Route::post('/reservation/notif', [ResortController::class, 'notifiReservation']);//changed create to notifiReservation

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/users', [UserController::class, 'getAllUser']);

    Route::post('/user/update/owner', [UserController::class, 'updateToOwner']);
    Route::get('/resort/list', [ResortController::class, 'getResortList']);
    Route::get('/resort/list/capture', [ResortController::class, 'getCaptureResortList']);
    Route::post('/resort/create', [ResortController::class, 'create']);
    Route::post('/resort/create/reservation', [ResortController::class, 'createReservation']);

    //
    // NOTIFICATION
    //
    Route::get('/notification', [NotificationController::class, 'index']);
    Route::get('/notification/show', [NotificationController::class, 'show']);
    Route::post('/notification/create', [NotificationController::class, 'create']);
    Route::post('/notification/update', [NotificationController::class, 'update']);
    // Route::post('/notification/review', [ResortController::class, 'notifiReservation']);


    //
    // CHAT MESSAGES
    //
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats/create', [ChatController::class, 'create']);






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
