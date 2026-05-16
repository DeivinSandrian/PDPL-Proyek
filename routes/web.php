<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StopPointController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\VehicleController as AdminVehicleController;
use App\Http\Controllers\Admin\RouteController as AdminRouteController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('vehicles', AdminVehicleController::class)->except(['show']);
    Route::resource('routes', AdminRouteController::class)->except(['show']);
    Route::resource('schedules', AdminScheduleController::class)->except(['show']);
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
});

/*
|--------------------------------------------------------------------------
| Customer Routes (R16.1)
|--------------------------------------------------------------------------
|
| All customer-facing self-service routes live under the `customer.` name
| prefix and `/customer` URL prefix, guarded by the `auth` and `customer`
| middleware stack. Controller classes referenced by these routes are
| implemented in subsequent tasks (5, 7, 8, 9, 10, 13, 16, 18, 19, 20);
| until then each route resolves to a 501 stub closure so that
| `php artisan route:list` and any links from existing views resolve
| without "Class not found" errors.
|
*/
Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {

    // --- Schedule search & detail (Task 5) ---------------------------------
    Route::get('schedules', fn () => abort(501, 'Not implemented yet'))
        ->name('schedules.index');
    Route::post('schedules/search', fn () => abort(501, 'Not implemented yet'))
        ->name('schedules.search');
    Route::get('schedules/{schedule}', fn () => abort(501, 'Not implemented yet'))
        ->name('schedules.show');

    // --- Seat map & seat hold (Tasks 7 & 8) --------------------------------
    Route::get('schedules/{schedule}/seats', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.seats');
    Route::get('schedules/{schedule}/availability', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.availability');
    Route::post('schedules/{schedule}/hold', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.holdSelection');

    // --- Booking history & detail (Task 16) --------------------------------
    Route::get('bookings', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.index');
    Route::get('bookings/search', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.searchByCode');
    Route::get('bookings/{booking}', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.show');

    // --- Pickup / dropoff selection (Task 9) -------------------------------
    Route::get('bookings/{booking}/stop-points', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.stop-points.edit');
    Route::patch('bookings/{booking}/stop-points', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.stop-points.update');

    // --- Passenger information (Task 10) -----------------------------------
    Route::get('bookings/{booking}/passengers', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.passengers.edit');
    Route::patch('bookings/{booking}/passengers', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.passengers.update');

    // --- Payment (Task 13) -------------------------------------------------
    Route::get('bookings/{booking}/payment', fn () => abort(501, 'Not implemented yet'))
        ->name('payment.show');
    Route::post('bookings/{booking}/payment', fn () => abort(501, 'Not implemented yet'))
        ->name('payment.initiate');

    // --- E-ticket download (Task 18) ---------------------------------------
    Route::get('bookings/{booking}/eticket', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.eticket.download');

    // --- Cancellation request (Task 19) ------------------------------------
    Route::get('bookings/{booking}/cancellation', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.cancellation.create');
    Route::post('bookings/{booking}/cancellation', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.cancellation.store');

    // --- Reschedule request (Task 20) --------------------------------------
    Route::get('bookings/{booking}/reschedule', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.reschedule.create');
    Route::post('bookings/{booking}/reschedule', fn () => abort(501, 'Not implemented yet'))
        ->name('bookings.reschedule.store');
});

/*
|--------------------------------------------------------------------------
| Midtrans payment callback (server-to-server webhook)
|--------------------------------------------------------------------------
|
| Lives OUTSIDE the auth/customer group because Midtrans posts directly
| from its own servers. The payload is signature-verified inside the
| handler (`PaymentService::handleNotification`) and the route is also
| exempt from CSRF (configured in bootstrap/app.php in Task 13).
|
*/
Route::post('payment/callback', fn () => abort(501, 'Not implemented yet'))
    ->name('customer.payment.callback');

require __DIR__.'/auth.php';
