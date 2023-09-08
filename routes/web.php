<?php

// use Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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
    Artisan::call('optimize');
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

