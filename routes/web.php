<?php

// use Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

// use Illuminate\Support\Facades\Artisan;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/optimize', function () {
    try {
        // Use Artisan::call to run the storage:link command
        Illuminate\Support\Facades\Artisan::call('optimize');

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
        Artisan::call('storage:link');

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


// The Email Verification Handler
Route::get('/email/verify/{id}/{hash}', function(Request $request){

    try{
        $user = User::findOrFail($request->route('id')); 

    // Check if the user is already verified to avoid unnecessary updates
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect('https://quickrent.online/signin');
    
    }catch (\Exception $e) {

        return response()->json(['response' => $e->getMessage()]);

    }

})->middleware(['signed'])->name('verification.verify');


// Resending The Verification Email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['response'=> 'Verification link sent!']);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');









