<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;



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

Route::get('/image', function (Request $request) {
    // Serve your image here
    // $imagePath = storage_path('app/public/'.$request->image_url); // Adjust the path
    $imagePath = $request->image_path;
    
    $image = file_get_contents($imagePath);

    return (new Response($image, 200))
        ->header('Content-Type', 'image/jpeg')
        ->header('Access-Control-Allow-Origin', '*'); // Adjust to your needs
});


// Resending The Verification Email
Route::post('/email/resend-verification', function (Request $request) {
    try{
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'response' => 'User not found']);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => 'error', 'response' => 'Email is already verified']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => 'success', 'response' => 'Verification link sent!']);

    }catch (\Exception $e) {

        return response()->json([
            'status' => 'error',
            'response' => $e->getMessage(),
        ]);
        
    }

})->middleware(['throttle:6,1']);


// The Email Password Reset Notice
Route::post('/forgot-password', function (Request $request) {

    try{
        $request->validate(['email' => 'required|email']);

        $userInfo = User::where('email', $request->email)->count();

        if($userInfo === 0){
            return response()->json([
                'status' => 'error',
                'response' => 'Email not found',
            ]);
        }
 
        $status = Password::sendResetLink(
            $request->only('email'),
            
        );
    
        return $status === Password::RESET_LINK_SENT
                    ? response()->json(['status' => 'success', 'response' => 'Password reset link sent successfully '])
                    : response()->json(['status' => 'error', 'response' => __($status)], 400);
                    
    } catch (\Exception $e) {

        return response()->json([
            'status' => 'error',
            'response' => $e->getMessage(),
        ]);
        
    }

})->middleware('guest')->name('password.email');


Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');


// The Reset Password Handler
Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
 
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
 
            $user->save();
 
            event(new PasswordReset($user));
        }
    );
 
    return $status === Password::PASSWORD_RESET
                ? response()->json(['status' => 'success', 'response' => 'Password successfully reset'])
                : response()->json(['status' => 'error', 'response' => __($status)], 422);
})->middleware('guest')->name('password.update');



Route::get('/login', [LoginController::class, 'index'])->name('login');

Route::post('/login', [LoginController::class, 'create']);

Route::post('/register', [LoginController::class, 'store']);

Route::get('/resorts', [ResortController::class, 'index']);

Route::get('/resorts/show', [ResortController::class, 'indexShow']);



Route::post('/pusher/test', [ChatController::class, 'testingPusher']);



Route::middleware(['auth:sanctum', 'cors', 'throttle:60,1'])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    //ADMIN
    Route::get('/reports', [AdminController::class, 'index']);
    Route::get('/logs', [ActivityLogController::class, 'index']);
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
    Route::get('/notifications/admin', [NotificationController::class, 'adminNotifications']);
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
    Route::post('/resort/create/reservation', [ReservationController::class, 'createReservation']);
    Route::post('/reservations/notify', [NotificationController::class, 'notifiReservation']);
    Route::post('/resort/reservation/confirm', [ReservationController::class, 'confirmReservation']);

    //
    // REVIEWS
    //
    Route::get('/review/show', [ResortController::class, 'showResortReview']);

    //
    // AUTH LOGOUT
    //
    Route::post('/logout', [LoginController::class, 'destroy']);
});
