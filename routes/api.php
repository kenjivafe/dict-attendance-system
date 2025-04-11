<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\SendPosition;
use App\Events\TestEvent;
use App\Http\Controllers\PositionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Test route to verify API routing is working
Route::get('/test', function() {
    // return response()->json(['message' => 'Test successful']);

    event(new TestEvent());
});

// Route to set current location in session
Route::post('/set-location', function (Request $request) {
    $validated = $request->validate([
        'lat' => 'required|numeric',
        'long' => 'required|numeric'
    ]);

    // Debug: dump and die to see exact input
    dd([
        'input_lat' => $request->input('lat'),
        'input_long' => $request->input('long'),
        'validated_lat' => $validated['lat'],
        'validated_long' => $validated['long']
    ]);

    // Store location in session
    session(['current_location' => [
        'lat' => $validated['lat'],
        'long' => $validated['long']
    ]]);

    return response()->json([
        'status' => 'success',
        'message' => 'Location set successfully'
    ]);
});
