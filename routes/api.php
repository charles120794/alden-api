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
use App\Http\Controllers\ReservationController;

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
use Illuminate\Http\Response;

Route::get('/image', function (Request $request) {
    // Serve your image here
    // $imagePath = storage_path('app/public/'.$request->image_url); // Adjust the path
    $imagePath = $request->image_path;
    
    $image = file_get_contents($imagePath);

    return (new Response($image, 200))
        ->header('Content-Type', 'image/jpeg')
        ->header('Access-Control-Allow-Origin', '*'); // Adjust to your needs
});

Route::get('/login', [LoginController::class, 'index'])->name('login');

Route::post('/login', [LoginController::class, 'create']);

Route::post('/register', [LoginController::class, 'store']);

Route::get('/units', [PublicUnitController::class, 'index']);

Route::get('/resorts', [ResortController::class, 'index']);

Route::get('/resorts/show', [ResortController::class, 'indexShow']);

Route::post('/alden/api', function () {

    foreach(request()->file('resort_image') as $key => $file) {

        $filename = time() . '_' . $file->getClientOriginalName();

        // You can choose a storage disk here
        $file->storeAs('public', $filename);
        
        // You can also save the filename to a database if needed
        $getFileName = Storage::disk('public')->url($filename);
    }
});

Route::post('/pusher/test', [ChatController::class, 'testingPusher']);

Route::post('/reservation/notif', [ResortController::class, 'notifiReservation']);//changed create to notifiReservation

Route::middleware(['auth:sanctum', 'cors'])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // USERS
    Route::get('/users', [UserController::class, 'getAllUser']);
    Route::get('/users/pending', [UserController::class, 'getAllPendingUser']);
    Route::post('/user/update', [UserController::class, 'updateProfile']);
    Route::post('/user/update/owner', [UserController::class, 'updateToOwner']);
    Route::post('/users/approve', [UserController::class, 'approveUserToOwner']);

    //RESERVATION
    Route::post('/resort/reservation/confirm', [ResortController::class, 'confirmReservation']);


    Route::get('/resort/list', [ResortController::class, 'getResortList']);
    Route::get('/resort/list/capture', [ResortController::class, 'getCaptureResortList']);
    Route::post('/resort/create', [ResortController::class, 'create']);
    Route::post('/resort/create/reservation', [ResortController::class, 'createReservation']);
    Route::post('/resort/create/images', [ResortController::class, 'uploadResortImages']);
    Route::get('/resort/list/reservations', [ReservationController::class, 'index']);


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
    Route::get('/chats/show', [ChatController::class, 'indexShow']);
    Route::post('/chats/create', [ChatController::class, 'create']);
    Route::post('/chats/read', [ChatController::class, 'updateReadStatus']);
    Route::post('/chats/unread', [ChatController::class, 'unreadStatus']);






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
