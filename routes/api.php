<?php

use App\Models\Power;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/daya', function (Request $request) {
    $powers = Power::paginate(20);

    return response()->json([
        'success' => true,
        'data' => $powers
    ]);
});

Route::post('/daya', function (Request $request) {
    $validatedRequest = $request->validate([
        'koneksi' => 'required|in:BLE,WIFI',
        'daya' => 'required|integer'
    ]);

    $power = Power::create($validatedRequest);

    return response()->json([
        'success' => true,
        'message' => 'Power record created successfully',
        'data' => $power
    ], 201);
});
