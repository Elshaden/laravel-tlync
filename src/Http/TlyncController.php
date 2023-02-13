<?php

namespace Elshaden\Tlync\Http;

use Elshaden\Tlync\Tlync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TlyncController
{

    public function __construct()
    {
        //
    }

    public function callback(Request $request)
    {
        Log::info('TlyncController 18: ' . json_encode($request->all()));
        $response = app(Tlync::class)->callback($request);
        return response()->json(null, 204);
    }
}
