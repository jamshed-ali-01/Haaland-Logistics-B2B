<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'warehouses' => \App\Models\Warehouse::all(),
        'countries' => \App\Models\Country::with('regions')->get(),
    ]);
});

Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
Route::get('/quotes/calculate', [QuoteController::class, 'calculate'])->name('quotes.calculate');

Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::post('/users/{user}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
    Route::post('/users/{user}/pause', [AdminController::class, 'pauseUser'])->name('users.pause');
    
    Route::get('/quotes', [AdminController::class, 'quotes'])->name('quotes.index');
    Route::get('/quotes/{quote}', [AdminController::class, 'showQuote'])->name('quotes.show');
    Route::post('/quotes/{quote}/accept', [AdminController::class, 'acceptQuote'])->name('quotes.accept');
    Route::post('/quotes/{quote}/reject', [AdminController::class, 'rejectQuote'])->name('quotes.reject');
    Route::post('/quotes/{quote}/notes', [AdminController::class, 'updateQuoteNotes'])->name('quotes.update-notes');
    Route::get('/leads', [AdminController::class, 'leads'])->name('leads');
    
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/test-email', [AdminController::class, 'testEmail'])->name('settings.test-email');
    
    Route::get('/rates', [AdminController::class, 'rates'])->name('rates');
    Route::post('/rates', [AdminController::class, 'storeRate'])->name('rates.store');
    Route::patch('/rates/{rate}', [AdminController::class, 'updateRate'])->name('rates.update');
    Route::delete('/rates/{rate}', [AdminController::class, 'destroyRate'])->name('rates.destroy');
    
    Route::get('/departures', [AdminController::class, 'departures'])->name('departures');
    Route::post('/departures', [AdminController::class, 'storeDeparture'])->name('departures.store');
    Route::patch('/departures/{departure}', [AdminController::class, 'updateDeparture'])->name('departures.update');
    Route::delete('/departures/{departure}', [AdminController::class, 'destroyDeparture'])->name('departures.destroy');
    
    Route::get('/warehouses', [AdminController::class, 'warehouses'])->name('warehouses');
    Route::post('/warehouses', [AdminController::class, 'storeWarehouse'])->name('warehouses.store');
    Route::patch('/warehouses/{warehouse}', [AdminController::class, 'updateWarehouse'])->name('warehouses.update');
    Route::delete('/warehouses/{warehouse}', [AdminController::class, 'destroyWarehouse'])->name('warehouses.destroy');
    Route::post('/warehouses/poe-mapping', [AdminController::class, 'storePoeMapping'])->name('warehouses.poe-mapping.store');
    Route::delete('/warehouses/poe-mapping/{mapping}', [AdminController::class, 'destroyPoeMapping'])->name('warehouses.poe-mapping.destroy');
    
    Route::get('/external-shipments', [AdminController::class, 'externalShipments'])->name('external-shipments');
    Route::post('/external-shipments', [AdminController::class, 'storeExternalShipment'])->name('external-shipments.store');
    Route::get('/operations', [AdminController::class, 'operations'])->name('ops.index');

    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');
    Route::post('/bookings/{booking}/toggle-status', [AdminController::class, 'toggleBookingStatus'])->name('bookings.toggle-status');

    Route::get('/services', [AdminController::class, 'services'])->name('services');
    Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
    Route::patch('/services/{service}', [AdminController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [AdminController::class, 'destroyService'])->name('services.destroy');

    Route::get('/service-types', [AdminController::class, 'serviceTypes'])->name('service-types');
    Route::post('/service-types', [AdminController::class, 'storeServiceType'])->name('service-types.store');
    Route::patch('/service-types/{serviceType}', [AdminController::class, 'updateServiceType'])->name('service-types.update');
    Route::delete('/service-types/{serviceType}', [AdminController::class, 'destroyServiceType'])->name('service-types.destroy');
});
Route::middleware('auth')->group(function () {
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create', [QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/quote/{quote}/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings/quote/{quote}', [BookingController::class, 'store'])->name('bookings.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
});

Route::get('/migrate', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate');
    return 'Database migrated successfully!';
});

require __DIR__.'/auth.php';

