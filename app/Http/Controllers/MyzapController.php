<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ApiGratis\ApiBrasil;
use App\Models\Sale;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class MyzapController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('auth');

        parent::__construct();
    }

    private function checkPerfilUsuario(Request $request)
    {
        if (!$this->validatePerfil($request->user())) {
            Auth::logout();
            return redirect('/home');
        }
    }

    public function start(Request $request)
    {
        try {
            $this->checkPerfilUsuario($request);

            $user = $request->user();
            $session = $this->getUserPhone($user);

            $serverhost = env('MYZAP_URL') . '/start';
            $token = env('MYZAP_TOKEN');
            $headers = [
                'Content-Type' => 'application/json',
                'apitoken' => $token,
                "sessionkey" => env('MYZAP_SESSION_KEY')
                
            ];
            $body = [
                "session" => $session,
            ];
            $jsonResp = Http::withHeaders($headers)->post($serverhost, $body);

            $result = json_decode($jsonResp, true);

            return response()->json($result);

            // $body = [
            //     "serverhost" => env('MYZAP_URL'),
            //     "sessionkey" => env('MYZAP_SESSION_KEY'),
            //     "apitoken" => env('MYZAP_TOKEN'),
            //     "session" => $session,
            // ];

            // $json = ApiBrasil::WhatsAppService('start', $body);
            // $result = json_decode($json, true);

            // return response()->json($result);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    function getUserPhone($user)
    {
        $phone = preg_replace("/[^0-9]/", "", $user->phone);
        return strval($phone);
    }

    public function getQrCode($session)
    {
        $url = sprintf("%s/getqrcode?session=%s&sessionkey=%s", env('MYZAP_URL'), $session, env('MYZAP_SESSION_KEY'));

        return response($url);
    }

    public function sendTicket(Request $request, Sale $sale)
    {
        if (empty($request->input('session'))) {
            return response('Parametro session nao informado', 401);
        }

        $session = $request->input('session');
        $seller = $sale->vendedor;
        $sellerPhone = $seller->phone;
        $sellerPhone = preg_replace("/[^0-9]/", "", $sellerPhone);

        $message = '';
        for ($i = 0; $i < 3; $i++) {
            switch ($i) {
                case 0:
                    $message = 'Olá ' . $seller->name;
                    break;

                case 1:
                    $message = 'Segue bilhete do sr(a) *' . $sale->buyer . '*';
                    break;

                case 2:
                    $message = env('APP_URL') . '/assets/img/bilhete_2.png';
                    break;

                default:
                    $message = '';
                    break;
            }
            if (!$this->sendText($session, $sellerPhone, $message)) {
                throw new Exception("Falha ao enviar mensagem no whatsapp");
            }
        }

        return response("Mensagem enviada com sucesso");
    }

    private function sendText($session, $sellerPhone, $message)
    {
        $serverhost = env('MYZAP_URL') . '/sendText';
        $headers = [
            "sessionkey" => env('MYZAP_SESSION_KEY'),
            'Content-Type' => 'application/json'
        ];
        $body = [
            "session" => $session,
            'number' => '+55' . $sellerPhone,
            'text' => $message
        ];
        $jsonResp = Http::withHeaders($headers)->post($serverhost, $body);

        return $jsonResp->status() == 200;
    }
}
