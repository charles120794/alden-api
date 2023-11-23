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
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CaptureRequestController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\VerificationController;


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

    // The Email Verification Notice
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');


    // The Email Verification Handler
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');


    // Resending The Verification Email
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
    
        return response()->json(['response'=> 'Verification link sent!']);
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::get('/login', [LoginController::class, 'index'])->name('login');

Route::post('/login', [LoginController::class, 'create']);

Route::post('/register', [LoginController::class, 'store']);

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

Route::middleware(['auth:sanctum', 'cors'])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    //ADMIN
    Route::get('/reports', [AdminController::class, 'index']);
    Route::post('/activity/create', [ActivityLogController::class, 'create']);

    // USERS
    Route::get('/users', [UserController::class, 'getAllUser']);
    Route::get('/users/pending', [UserController::class, 'getAllPendingUser']);
    Route::post('/user/update', [UserController::class, 'updateProfile']);
    Route::post('/user/update/owner', [UserController::class, 'updateToOwner']);
    Route::post('/users/approve', [UserController::class, 'approveUserToOwner']);
    Route::post('/user/paymethod/create', [UserController::class, 'addPaymentMethod']);
    Route::post('/user/paymethod/delete', [UserController::class, 'deletePaymentMethod']);
    Route::get('/user/bookmarks', [UserController::class, 'allBookmarks']);
    Route::post('/user/bookmarks/update', [UserController::class, 'updateBookmarks']);


    Route::get('/resort/list', [ResortController::class, 'getResortList']);
    Route::get('/resort/list/capture', [CaptureRequestController::class, 'index']);
    Route::post('/resort/request/capture', [CaptureRequestController::class, 'create']);
    Route::post('/resort/update/capture', [CaptureRequestController::class, 'update']);
    Route::post('/resort/delete/images', [CaptureRequestController::class, 'deleteImage']);

    Route::post('/resort/create', [ResortController::class, 'create']);
    Route::post('/resort/update', [ResortController::class, 'update']);

    Route::get('/resort/list/reservations', [ReservationController::class, 'index']);
    Route::post('/resort/review/create', [ResortController::class, 'reviewResort']);


    //
    // NOTIFICATION
    //
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notification/show', [NotificationController::class, 'show']);
    Route::post('/notification/create', [NotificationController::class, 'create']);
    Route::post('/notification/submit', [NotificationController::class, 'submit']);
    Route::post('/notification/update', [NotificationController::class, 'update']);
    // Route::post('/notification/review', [NotificationController::class, 'rateReservation']);


    //
    // CHAT MESSAGES
    //
    Route::get('/chats', [ChatController::class, 'index']);
    Route::get('/chats/show', [ChatController::class, 'indexShow']);
    Route::post('/chats/create', [ChatController::class, 'create']);
    Route::post('/chats/read', [ChatController::class, 'updateReadStatus']);
    Route::post('/chats/unread', [ChatController::class, 'unreadStatus']);

    //
    // RESERVATIONS
    //
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/resort/owner/dashboard', [ReservationController::class, 'ownderDashboard']);
    Route::post('/resort/create/reservation', [ResortController::class, 'createReservation']);
    Route::post('/reservations/notify', [NotificationController::class, 'notifiReservation']);
    Route::post('/resort/reservation/confirm', [ResortController::class, 'confirmReservation']);

    //
    // REVIEWS
    //
    Route::get('/review/show', [ResortController::class, 'showResortReview']);

    //
    // AUTH LOGOUT
    //
    Route::post('/logout', [LoginController::class, 'destroy']);
});
