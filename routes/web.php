<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LeadController;
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
    
    Route::get('/leads', [AdminController::class, 'leads'])->name('leads');
    
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    
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
    
    Route::get('/external-shipments', [AdminController::class, 'externalShipments'])->name('external-shipments');
    Route::post('/external-shipments', [AdminController::class, 'storeExternalShipment'])->name('external-shipments.store');
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
});

require __DIR__.'/auth.php';
