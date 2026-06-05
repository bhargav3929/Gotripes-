<?php

use App\Http\Controllers\FlightApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Farenexus nexusAPI — Flight booking lifecycle
|--------------------------------------------------------------------------
| search -> price -> book -> checkout (pay) -> ticket, plus post-booking ops.
*/
Route::prefix('flights')->controller(FlightApiController::class)->group(function () {
    // Shopping
    Route::post('search', 'search');
    Route::post('price', 'price');
    Route::post('fare-rules', 'fareRules');
    Route::post('seatmap', 'seatMap');
    Route::post('ancillaries', 'ancillaries');

    // Booking + payment + ticketing
    Route::post('book', 'book');
    Route::post('checkout', 'checkout');
    Route::post('ticket', 'ticket');

    // Post-booking
    Route::get('booking/{orderId}', 'show');
    Route::post('cancel', 'cancel');
    Route::post('refund', 'refund');
});
