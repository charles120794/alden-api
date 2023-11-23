<?php

// use Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\VerificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;


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
// The Email Verification Handler
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');




// Resending The Verification Email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['response'=> 'Verification link sent!']);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
 
    $status = Password::sendResetLink(
        $request->only('email')
    );
 
    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');





Route::get('/', function () {
    return view('welcome');
});

Route::get('/optimize', function () {
    try {
        // Use Artisan::call to run the storage:link command
        \Illuminate\Support\Facades\Artisan::call('optimize');

        // Provide a success message
        return 'Optimize successfully.';
    } catch (\Exception $e) {
        // Handle any exceptions that may occur
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/storage/link', function () {
    // Artisan::call('storage:link');
    try {
        // Use Artisan::call to run the storage:link command
        \Illuminate\Support\Facades\Artisan::call('storage:link');

        // Provide a success message
        return 'Storage link created successfully.';
    } catch (\Exception $e) {
        // Handle any exceptions that may occur
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

    // The Email Verification Notice
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


