<?php

namespace Elshaden\Tlync;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

use App\Models\User;
use phpDocumentor\Reflection\Types\Integer;
use Vinkla\Hashids\Facades\Hashids;

class Tlync
{
protected $url;
protected $token;
    public function __construct()
    {
        $this->url = in_array( config('tlync.tlync_environment'),['local', 'uat', 'test']) ? config('tlync.tlync_test_url') : config('tlync.tlync_live_url');
        $this->token = in_array( config('tlync.tlync_environment'),['local', 'uat', 'test']) ? config('tlync.tlync_test_token') : config('tlync.tlync_live_token');

    }



    public function InitiatePayment(float $Amount, integer $para_1,integer $para_2, string $UserPhone, string $UserEmail = Null)
    {
         $HashedId = Hashids::encode($para_1);
         $HashedTenantId = Hashids::encode($para_2);

        $payload = [
            'id' =>  in_array( config('tlync.tlync_environment'),['local', 'uat', 'test']) ? config('tlync.tlync_test_store_id') : config('tlync.tlync_live_store_id'),
            'amount' => $Amount,
            'phone' => $UserPhone,
            'email' => $UserEmail,
            'backend_url' => config('tlync.callback_url'),
            'frontend_url' =>config('tlync.frontend_url') .$para_1,
            'custom_ref' => $HashedId.'|'.rand(100,10000).'|'.$HashedTenantId,
        ];
        $payload = array_filter($payload);
        ray($payload);
        $endpoint = 'payment/initiate';

        $Response = $this->SendRequest($endpoint, $payload);
        if(isset($Response['result']) && $Response['result'] == 'success'){
        //    Log::info('Tlync Payment Initiated', ['Response'=>$Response]);

            return ['Response'=>true, 'message'=>'redirect to url', 'url'=>$Response['response']['url']];

         }else{

            return ['Response'=>false, 'message'=>$Response?? 'Failed ToConnect to Tlync System'];
        }
    }


    public function SendRequest($endpoint, $payload)
    {
        $url = $this->url . $endpoint;
        $token = $this->token;
        try {
            $Response = Http::withHeaders(['Accept'=>'application/json'])->withToken($token)->post($url, $payload);
        } catch (\Exception $e) {
     //       Log::error($e);
            return ['Response'=>false, 'message'=>$e?? 'Failed ToConnect to Tlync System', 'code'=>$e->getCode()];
        }

        if ($Response->successful()) {
            return [
                'result'=>true,
                'response'=>$Response->json()
            ];

        } else {
            return $Response->body();
        }
    }




    public function callback(Request $request)
    {

        $request->validate(['custom_ref'=>'required']);


        $Paras = explode('|', $request->custom_ref);
        try {
            $para_1 = Hashids::decode($Paras[0])[0];
            $para_2 = Hashids::decode($Paras[2])[0];
        } catch (\Exception $e) {
            return False;
        }
        if (! $para_1 || ! $para_2) {
            return False;
        }

        app(config('tlync.callback_class'))->config('tlync.callback_class')($para_1, $para_2, $request->all());
        return 'ok';
    }
}
