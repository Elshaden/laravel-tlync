<?php

namespace Egate\Tlync\Http;

use Egate\Tlync\Tlync;
use Illuminate\Http\Request;

class TlyncController
{

    public function __construct()
    {
        //
    }

    public function callback(Request $request)
   {
     $response =  app(Tlync::class)->callback($request);
       return response()->json($response ,200);
   //  return response()->json(Null, 200);
    }
}