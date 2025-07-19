<?php

use App\Http\Controllers\BusController;
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

    Route::post('/preview-booking', [BookingController::class, 'preview'])->name('booking.preview');
    Route::post('/create-booking', [BookingController::class, 'create'])->name('booking.create');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
});

Route::middleware([EnsureAdmin::class])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/buses', [BusController::class, 'index'])->name('admin.buses.index');
    Route::get('/admin/buses/create', [BusController::class, 'create'])->name('admin.buses.create');
    Route::post('/admin/buses', [BusController::class, 'store'])->name('admin.buses.store');
    Route::delete('/admin/buses/{bus}', [BusController::class, 'destroy'])->name('admin.buses.destroy');

    Route::get('/admin/festivals', [FestivalController::class, 'adminIndex'])->name('admin.festivals.index');
    Route::get('/admin/festivals/create', [FestivalController::class, 'create'])->name('admin.festivals.create');
    Route::post('/admin/festivals', [FestivalController::class, 'store'])->name('admin.festivals.store');
    Route::get('/admin/festivals/{festival}/edit', [FestivalController::class, 'edit'])->name('admin.festivals.edit');
    Route::put('/admin/festivals/{festival}', [FestivalController::class, 'update'])->name('admin.festivals.update');
    Route::delete('/admin/festivals/{festival}', [FestivalController::class, 'destroy'])->name('admin.festivals.destroy');
    Route::post('/admin/festivals/{festival}/toggle', [FestivalController::class, 'toggle'])->name('admin.festivals.toggle');

    Route::get('/admin/bookings', [BookingController::class, 'adminIndex'])->name('admin.bookings.index');
    Route::get('/admin/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('admin.bookings.edit');
    Route::put('/admin/bookings/{booking}', [BookingController::class, 'update'])->name('admin.bookings.update');
    Route::delete('/admin/bookings/{booking}', [BookingController::class, 'destroy'])->name('admin.bookings.destroy');

    Route::get('/admin/rewards', [RewardController::class, 'adminIndex'])->name('admin.rewards.index');
    Route::get('/admin/rewards/create', [RewardController::class, 'create'])->name('admin.rewards.create');
    Route::post('/admin/rewards', [RewardController::class, 'store'])->name('admin.rewards.store');
    Route::get('/admin/rewards/{reward}/edit', [RewardController::class, 'edit'])->name('admin.rewards.edit');
    Route::put('/admin/rewards/{reward}', [RewardController::class, 'update'])->name('admin.rewards.update');
    Route::delete('/admin/rewards/{reward}', [RewardController::class, 'destroy'])->name('admin.rewards.destroy');

    Route::get('/admin/users', [ProfileController::class, 'adminIndex'])->name('admin.users.index');
    Route::get('/admin/users/{user}/edit', [ProfileController::class, 'adminEdit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [ProfileController::class, 'adminUpdate'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [ProfileController::class, 'adminDestroy'])->name('admin.users.destroy');
});

require __DIR__.'/auth.php';
