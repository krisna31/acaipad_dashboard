<?php

use App\Models\Power;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', function(Request $request) {
    return response()->json([
        'success' => true, 
        'message' => 'pong'
    ]);
});

Route::get('/latency', function (Request $request) {
    abort_if(config('app.secret_key') == '' || config('app.secret_key') == null, 501);
    abort_if($request->secret_key != config('app.secret_key'), 404);

    $powers = Power::select(
            '*',
            DB::raw('strftime("%Y-%m-%d %H:%M:%S", created_at) as created_at_human'),
            DB::raw('strftime("%Y-%m-%d %H:%M:%S", updated_at) as updated_at_human'),
            DB::raw('strftime("%Y-%m-%d %H:%M:%S", sent_at) as sent_at_human'),
            DB::raw('strftime("%s", created_at) - strftime("%s", sent_at) as diff_seconds'),
        )
        ->paginate(20);

    return response()->json([
        'success' => true,
        'data' => $powers
    ]);
});

// Route::get('/daya', function (Request $request) {
//     abort_if(config('app.secret_key') == '' || config('app.secret_key') == null, 501);
//     abort_if($request->secret_key != config('app.secret_key'), 404);

//     $powers = Power::paginate(20);

//     return response()->json([
//         'success' => true,
//         'data' => $powers
//     ]);
// });

// Route::post('/daya', function (Request $request) {
//     abort_if(config('app.secret_key') == '' || config('app.secret_key') == null, 501);
//     abort_if($request->secret_key != config('app.secret_key'), 404);

//     $validatedRequest = $request->validate([
//         'koneksi' => 'required|in:BLE,WIFI',
//         'daya' => 'required|integer',
//         'secret_key' => 'required|string',
//     ]);

//     $power = Power::create($validatedRequest);

//     return response()->json([
//         'success' => true,
//         'message' => 'Power record created successfully',
//         'data' => $power
//     ], 201);
// });
