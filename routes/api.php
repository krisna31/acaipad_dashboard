<?php

use App\Models\Power;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            DB::raw('strftime("%Y-%m-%d %H:%M:%f", arrived_at) as arrived_at_human'),
            DB::raw('strftime("%Y-%m-%d %H:%M:%f", updated_at) as updated_at_human'),
            DB::raw('strftime("%Y-%m-%d %H:%M:%f", sent_at) as sent_at_human'),
            DB::raw('
                (julianday(arrived_at) - julianday(sent_at)) * 24 * 60 * 60 * 1000 as diff_milliseconds
            '),
        )
        ->paginate(20);

    return response()->json([
        'success' => true,
        'data' => $powers
    ]);
});

Route::post('/latency', function (Request $request) {
    abort_if(config('app.secret_key') == '' || config('app.secret_key') == null, 501);
    abort_if($request->secret_key != config('app.secret_key'), 404);

    $validatedRequest = $request->validate([
        'location' => 'required|in:INTERNET,LOKAL',
        'sent_at' => 'required|date|before:tomorrow|date_format:Y-m-d H:i:s.v',
        'key_pressed' => 'required|string',
    ]);

    $validatedRequest['arrived_at'] = now()->format('Y-m-d H:i:s.v');

    $data = "";
    $message = "";
    $statusCode = 201;
    if ($validatedRequest['location'] == 'INTERNET') {
        $data = Power::create($validatedRequest);
        $message = "Power record created successfully (Internet)";
    } else {
        $data = [
            'secret_key' => config('app.secret_key'),
            'sent_at' => $validatedRequest['sent_at'],
            'location' => $validatedRequest['location'],
            'arrived_at' => now()->format('Y-m-d H:i:s.v'),
            'key_pressed' => $validatedRequest['key_pressed'],
        ];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post(config('app.lokal_send_url'), $data);

            if ($response->successful()) {
                Log::info('Successfully received response from external API', [
                    'response' => $response->json(),
                ]);
                $message = "Power record created successfully (Lokal Hit API)";
            } else {
                Log::error('Failed to get a successful response from the API', [
                    'status' => $response->status(),
                    'error' => $response->body(),
                ]);
                $message = "Power record failed to created (Lokal Hit API) " . json_encode($response->body());
                $statusCode = 500;
            }

        } catch(\Throwable $e) {
            Log::error('Failed to get a successful response from the API', [
                'status' => $e,
            ]);
            $message = "Power record failed to created (Lokal Hit API) " . json_encode($e);
            $success = false;
            $statusCode = 500;
        }
        $data = null;
    }

    return response()->json([
        'success' => $success ?? true,
        'message' => $message,
        'data' => $data
    ], $statusCode);
});

Route::post('/latency/lokal', function (Request $request) {
    abort_if(config('app.secret_key') == '' || config('app.secret_key') == null, 501);
    abort_if($request->secret_key != config('app.secret_key'), 404);

    $validatedRequest = $request->validate([
        'location' => 'required|in:INTERNET,LOKAL',
        'sent_at' => 'required|string|date_format:Y-m-d H:i:s.v',
        'arrived_at' => 'required|string|date_format:Y-m-d H:i:s.v',
        'key_pressed' => 'required|string',
    ]);

    if ($validatedRequest['location'] == 'INTERNET') {
        abort(403, "No Internet Allowed");
    } else {
        $power = Power::create($validatedRequest);
    }

    return response()->json([
        'success' => true,
        'message' => 'Power record created successfully',
        'data' => $power
    ], 201);
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
