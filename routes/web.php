<?php

use App\Http\Controllers\FestivalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RewardController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureAdmin;

Route::get('/', [FestivalController::class, 'home'])->name('welcome');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/dashboard', [BookingController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/festivals', [FestivalController::class, 'index'])->name('festivals.index');
Route::get('/festivals/{festival}', [FestivalController::class, 'show'])->name('festivals.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/redeem/{reward}', [RewardController::class, 'redeem'])->name('rewards.redeem');

    Route::post('/create-booking', [BookingController::class, 'create'])->name('booking.create');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
});

Route::middleware([EnsureAdmin::class])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

require __DIR__.'/auth.php';
