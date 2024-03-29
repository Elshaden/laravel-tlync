<?php

namespace Elshaden\Tlync;

use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class Tlync
{
    protected $url;

    protected $token;

    protected $HashIds;

    public function __construct()
    {
        if (config('tlync.force_test_mode')) {
            $this->url = config('tlync.tlync_test_url');
            $this->token = config('tlync.tlync_test_token');
        } else {
            $this->url = config('app.env') == 'production' ? config('tlync.tlync_live_url') : config('tlync.tlync_test_url');
            $this->token = config('app.env') == 'production' ? config('tlync.tlync_live_token') : config('tlync.tlync_test_token');
        }
        $this->HashIds = new Hashids(config('hashids.connections.tlync.salt'), config('hashids.connections.tlync.length'), config('hashids.connections.tlync.alphabet'));
    }


    /**
     * @param float $Amount
     * @param $para_1
     * @param $para_2
     * @param int $para_3
     * @param string $UserPhone
     * @param string|Null $UserEmail
     * @return array
     */
    public function InitiatePayment(float $Amount, $para_1, $para_2, int $para_3, string $UserPhone, string $UserEmail = Null)
    {
        if (config('tlync.force_test_mode')) {
            $store_id = config('tlync.api_test_key');
        } else {
            $store_id = config('app.env') == 'production' ? config('tlync.api_live_key') : config('tlync.api_test_key');
        }
        // $hashIds = new Hashids(config('hashids.connections.tlync.salt'), config('hashids.connections.tlync.length'), config('hashids.connections.tlync.alphabet'));
        $randomize = $this->HashIds->encode($para_3);
        $payload = [
            'id' => $store_id,
            'amount' => $Amount,
            'phone' => $UserPhone,
            'email' => $UserEmail,
            'backend_url' => config('tlync.callback_url'),
            'frontend_url' => config('tlync.frontend_url') . $para_1,
            'custom_ref' => $para_1 . '|' . $randomize . '|' . $para_2,
        ];
        $payload = array_filter($payload);
        Log::info('Tlync Payment Initiated PayLoad :', ['data' => $payload]);
        $endpoint = 'payment/initiate';

        $Response = $this->SendRequest($endpoint, $payload);
        if (isset($Response['result']) && $Response['result'] == 'success') {
            Log::info('Tlync Payment Initiated', ['Response' => $Response]);

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

        if (config('tlync.restrict_ip')) {
            if (!in_array($Ip, config('tlync.allowed_ips'))) {
                Log::alert('Received Payment From UnAuthorized IP', ['IP' => $Ip]);
                return False;
            }
        }

        $Paras = explode('|', $request->custom_ref);
        try {
            //$hashIds = new Hashids(config('hashids.connections.tlync.salt'), config('hashids.connections.tlync.length'));
            $Paras[1] = $this->HashIds->decode($Paras[1])[0];

            // $Paras[1] = Hashids::connection('tlync')->decode($Paras[1])[0];;


        } catch (\Exception $e) {
            Log::alert('Received Payment Do Not Match', ['custom_ref' => $request->all()]);
            return False;
        }

        $CallbackClass = config('tlync.handel_call_back_class');
        $CallBackMethod = config('tlync.handel_method');

        $Class = new $CallbackClass();
        $request = $request->all();
        $request['gateway_ip'] = $Ip;
        $Class->$CallBackMethod($Paras, $request);

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
