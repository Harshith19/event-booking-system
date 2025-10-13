<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::post('/events/create', [EventController::class, 'store']);
    Route::put('/events/update/{id}', [EventController::class, 'update']);
    Route::delete('/events/delete/{event}', [EventController::class, 'destroy']);

    // Tickets with double booking prevention
    Route::middleware('prevent.double.booking')->group(function () {
        Route::post('/events/{event}/tickets', [TicketController::class, 'store']);
        Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
        Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);
        
        // Bookings
        Route::post('/tickets/{ticket}/bookings', [BookingController::class, 'store']);
    });

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

    // Payments
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'processPayment']);
    Route::get('/payments/{id}', [PaymentController::class, 'show']);
});