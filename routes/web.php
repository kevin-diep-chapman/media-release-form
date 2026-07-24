<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MediaReleaseController;
use App\Http\Controllers\MediaReleaseImageController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public home redirects to my events
Route::get('/up', function () {
    return response()->noContent();
});

Route::get('/', function () {
    return redirect('/my-events');
});

// Media Release Form Submission (AJAX)
Route::post('/media-release', [MediaReleaseController::class, 'store'])
    ->middleware('throttle:media-release')
    ->name('media-release.store');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/my-events', [PublicController::class, 'permittedEvents'])->name('public.permitted-events');
    Route::get('/public/my-events', [PublicController::class, 'permittedEvents'])->name('public.my-events');

    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

    Route::get('/media-releases/{id}/photo', [MediaReleaseImageController::class, 'photo'])
        ->name('media-releases.photo');
    Route::get('/media-releases/{id}/signature', [MediaReleaseImageController::class, 'signature'])
        ->name('media-releases.signature');
});

// Dashboard (protected)
Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::middleware('admin')->get('media-releases', [MediaReleaseController::class, 'index'])->name('media-releases.index');
    Route::middleware('admin')->get('users', [UserController::class, 'index'])->name('users.index');
    Route::middleware('admin')->post('users', [UserController::class, 'store'])->name('users.store');
    Route::middleware('admin')->put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::middleware('admin')->get('users/create', function () {
        return redirect()->route('dashboard.users.index');
    });
    Route::get('media-releases/{id}', [MediaReleaseController::class, 'show'])->name('media-releases.show');
});
