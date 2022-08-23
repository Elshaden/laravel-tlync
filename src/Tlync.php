<?php

namespace Elshaden\Tlync;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

use App\Models\User;
use Vinkla\Hashids\Facades\Hashids;

class Tlync
{
    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url = in_array(config('tlync.tlync_environment'), ['local', 'uat', 'test']) ? config('tlync.tlync_test_url') : config('tlync.tlync_live_url');
        $this->token = in_array(config('tlync.tlync_environment'), ['local', 'uat', 'test']) ? config('tlync.tlync_test_token') : config('tlync.tlync_live_token');

    }


    public function InitiatePayment(float $Amount, int $para_1, int $para_2, string $UserPhone, string $UserEmail = Null)
    {
        $HashedId = Hashids::encode($para_1);
        $HashedTenantId = Hashids::encode($para_2);
        $randomize = random_int(1000000,99999999);
        $payload = [
            'id' => in_array(config('tlync.tlync_environment'), ['local', 'uat', 'test']) ? config('tlync.tlync_test_store_id') : config('tlync.tlync_live_store_id'),
            'amount' => $Amount,
            'phone' => $UserPhone,
            'email' => $UserEmail,
            'backend_url' => config('tlync.callback_url'),
            'frontend_url' => config('tlync.frontend_url') . $para_1,
            'custom_ref' => $HashedId . '|' . $randomize . '|' . $HashedTenantId,
        ];
        $payload = array_filter($payload);
        Log::info('Tlync Payment Initiated PayLoad :', ['data'=>$payload]);
        $endpoint = 'payment/initiate';

        $Response = $this->SendRequest($endpoint, $payload);
        if (isset($Response['result']) && $Response['result'] == 'success') {
              Log::info('Tlync Payment Initiated', ['Response'=>$Response]);

            return ['Response' => true, 'message' => 'redirect to url', 'url' => $Response['response']['url']];

        } else {

            return ['Response' => false, 'message' => $Response ?? 'Failed ToConnect to Tlync System'];
        }
    }


    public function SendRequest($endpoint, $payload)
    {
        $url = $this->url . $endpoint;
        $token = $this->token;
        try {
            $Response = Http::withHeaders(['Accept' => 'application/json'])->withToken($token)->post($url, $payload);
        } catch (\Exception $e) {
            //       Log::error($e);
            return ['Response' => false, 'message' => $e ?? 'Failed ToConnect to Tlync System', 'code' => $e->getCode()];
        }

        if ($Response->successful()) {
            return [
                'result' => true,
                'response' => $Response->json()
            ];

        } else {
            return $Response->body();
        }
    }


    public function callback(Request $request)
    {

        $request->validate(['custom_ref' => 'required']);
        $Ip = $this->getIp();
        Log::info('TylncCallback', ['Request' => $request->all(), 'IP' => $Ip]);


        $Paras = explode('|', $request->custom_ref);
        try {
            $para_1 = Hashids::decode($Paras[0])[0];
            $para_2 = Hashids::decode($Paras[2])[0];
        } catch (\Exception $e) {
            Log::alert('Received Payment Do Not Match', ['custom_ref' => $request->all()]);
            return False;
        }
        if (!$para_1 || !$para_2) {
            Log::alert('Received Payment Do Not Match', ['custom_ref' => $request->all()]);
            return False;
        }
        $CallbackClass = config('tlync.handel_call_back_class');
        $CallBackMethod = config('tlync.handel_method');

        $Class = new $CallbackClass();
        $request = $request->all();
        $request['gateway_ip'] = $Ip;
        $Class->$CallBackMethod($para_1, $para_2, $request);

    }

    /**
     * @return mixed|null
     */
    private function getIp()
    {

        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = null;

        return $ipaddress;
    }
}
