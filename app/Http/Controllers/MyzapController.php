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

            if (!empty($request->input('close')) && $request->input('close') == 'true') {
                $this->closeSession($user, $session);
            }

            $serverhost = env('MYZAP_URL') . '/start';
            $token = env('MYZAP_TOKEN');
            $headers = [
                'Content-Type' => 'application/json',
                'apitoken' => $token,
                "sessionkey" => $this->getSessionKey($user)
            ];
            $body = [
                "session" => $session,
            ];
            $jsonResp = Http::withHeaders($headers)->post($serverhost, $body);

            $result = json_decode($jsonResp, true);

            return response()->json($result);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    private function getUserPhone($user)
    {
        $phone = preg_replace("/[^0-9]/", "", $user->phone);
        return strval($phone);
    }

    private function getSessionKey($user)
    {
        // $phone = preg_replace("/[^0-9]/", "", $user->phone);
        // $arrName = explode(" ", $user->name);

        // return strval(strtolower($arrName[0] . '_sabia'));

        return env('MYZAP_SESSION_KEY');
    }

    public function close(Request $request)
    {
        $this->checkPerfilUsuario($request);

        $user = $request->user();
        $session = $this->getUserPhone($user);

        $this->closeSession($user, $session);

        return response()->noContent();
    }

    private function closeSession($user, $session)
    {
        $serverhost = env('MYZAP_URL') . '/close';
        $headers = [
            "sessionkey" => $this->getSessionKey($user),
            'Content-Type' => 'application/json'
        ];

        $response = Http::withHeaders($headers)->post($serverhost, ['session' => $session]);

        if ($response->status() != 200) {
            return $response->body();
        }

        $serverhost = env('MYZAP_URL') . '/logout';
        $headers = [
            "sessionkey" => $this->getSessionKey($user),
            'Content-Type' => 'application/json'
        ];

        $response = Http::withHeaders($headers)->post($serverhost, ['session' => $session]);

        return $response->body();
    }

    public function getQrCode($user, $session)
    {
        $sessionKey = $this->getSessionKey($user);
        $url = sprintf("%s/getqrcode?session=%s&sessionkey=%s", env('MYZAP_URL'), $session, $sessionKey);

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

        // if(env('APP_ENV') == 'local') {
        //     $sellerPhone = "6596618339";
        // }

        $hasText = true;
        if (!empty($request->input('hasText')) && strval($request->input('hasText')) == 'false') {
            $hasText = false;
        }

        if ($hasText) {
            $message = $this->getDefaultText();
            $this->sendText($request->user(), $session, $sellerPhone, $message, false);

            if (env('APP_ENV') == 'local') {
                $message = "http://itaimbemaquinas.com.br/wp-content/uploads/sites/79/2020/07/zap-vermelho.png"; // env('MYZAP_IMG_DIR') . $sale->billet_file;    
            } else {
                $message = env('APP_URL') . '/assets/img/billets/' . $sale->billet_file;
            }

            $this->sendText($request->user(), $session, $sellerPhone, $message, true);
        } else {
            if (env('APP_ENV') == 'local') {
                $message = "http://itaimbemaquinas.com.br/wp-content/uploads/sites/79/2020/07/zap-vermelho.png"; // env('MYZAP_IMG_DIR') . $sale->billet_file;    
            } else {
                $message = env('APP_URL') . '/assets/img/billets/' . $sale->billet_file;
            }

            $this->sendText($request->user(), $session, $sellerPhone, $message, true);
        }

        return response("Mensagem enviada com sucesso");
    }

    private function sendText($user, $session, $sellerPhone, $message, $isFile = false)
    {
        if (empty($message)) {
            return true;
        }

        $serverhost = env('MYZAP_URL');
        if ($isFile) {
            $serverhost .= '/sendImage';
        } else {
            $serverhost .= '/sendText';
        }

        $headers = [
            "sessionkey" => $this->getSessionKey($user),
            'Content-Type' => 'application/json'
        ];

        $body = [
            'session' => $session,
            'number' => '+55' . $sellerPhone,
            'text' => $message
        ];

        if ($isFile) {
            unset($body['text']);
            $body['path'] = $message;
        }

        $jsonResp = Http::withHeaders($headers)->post($serverhost, $body);

        if (env('APP_ENV') == 'local' && $jsonResp->status() != 200) {
            var_dump([
                'host' => $serverhost,
                'headers' => $headers,
                'body' => $body
            ]);
            dd($jsonResp->body());
        }

        return $jsonResp; //->status() == 200;
    }

    private function getDefaultText()
    {
        $text = <<<EOF
*Caro benfeitor*,

Somos gratos pelo gesto de estender a mão ao próximo e nos auxiliar a melhor atender todas as pessoas que vem até nós em busca de luz, de amor, de uma palavra amiga!
*Que Deus multiplique as bençãos em sua vida!*

O resultado do sorteio dos 3 prêmios será divulgado em nosso *site* e no nosso *Instagram* no dia 17 abril de 2022.

Nos acompanhe no Insta: instagram.com/ocantosabia
E em nosso site: ocantodosabia.com.br

*Equipe de Promoções*
EOF;

        return $text;
    }

    public function storeBillet(Request $request)
    {
        $filename = uniqid() . '.png';
        $img = $request->img;
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = env('MYZAP_IMG_DIR') . $filename;
        $success = file_put_contents($file, $data);

        if (!$success) {
            throw new Exception("Nao foi possivel gerar a imagem do bilhete.");
        }

        $sale = Sale::where('id', $request->saleId)->firstOrFail();
        $sale->billet_file = $filename;
        $sale->update();

        return response()->json([
            'file' => $file
        ]);
    }

    public function webhookStatus(Request $request)
    {
        $filename = '/var/www/html/sabia-site/storage/logs/myzapStatus.log';
        try {
            $myfile = fopen($filename, "a");
            $txt = $request->json();
            fwrite($myfile, json_encode($txt, JSON_PRETTY_PRINT));
            fclose($myfile);
        } catch (\Throwable $th) {
            return response($th->getMessage(), 500);
        }
    }

    public function webhookConnect(Request $request)
    {
        $filename = '/var/www/html/sabia-site/storage/logs/myzapConnect.log';
        try {
            $myfile = fopen($filename, "a");
            $txt = $request->json();
            fwrite($myfile, json_encode($txt, JSON_PRETTY_PRINT));
            fclose($myfile);
        } catch (\Throwable $th) {
            return response($th->getMessage(), 500);
        }
    }

    public function webhookQrcode(Request $request)
    {
        $filename = '/var/www/html/sabia-site/storage/logs/myzapQrcode.log';
        try {
            $myfile = fopen($filename, "a");
            $txt = $request->json();
            fwrite($myfile, json_encode($txt, JSON_PRETTY_PRINT));
            fclose($myfile);
        } catch (\Throwable $th) {
            return response($th->getMessage(), 500);
        }
    }
}
