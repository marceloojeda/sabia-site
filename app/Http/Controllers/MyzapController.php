<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ApiGratis\ApiBrasil;
use App\Models\MyzapSession;
use App\Models\Sale;
use Exception;
use Illuminate\Support\Facades\Auth;
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
            $myzapSession = $this->getMyzapSession($user, $session);

            $serverhost = env('MYZAP_URL') . '/start';
            $token = env('MYZAP_TOKEN');
            $headers = [
                'Content-Type' => 'application/json',
                'apitoken' => $token,
                "sessionkey" => $myzapSession->session_key
            ];
            $body = [
                "session" => $session,
                "wh_status" => env('APP_URL') . '/myzap/webhook?user=' . $user->id,
                "wh_connect" => env('APP_URL') . '/myzap/webhook?user=' . $user->id,
                "wh_qrcode" => env('APP_URL') . '/myzap/webhook?user=' . $user->id
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

    private function getMyzapSession($user, $session)
    {
        $myzapSession = MyzapSession::where('user_id', $user->id)->first();
        if(!empty($myzapSession->session_key)) {
            return $myzapSession;
        }

        $sessionKey = $this->getUserPhone($user) . ".sabia";
        $arrSession = [
            'user_id' => $user->id,
            'session' => $session,
            'session_key' => $sessionKey,
        ];

        $myzapSession = MyzapSession::create($arrSession);
        $myzapSession->refresh();
        
        return $myzapSession;
    }

    public function close(Request $request, $session)
    {
        $this->checkPerfilUsuario($request);

        $user = $request->user();
        // $session = $this->getUserPhone($user);

        $this->closeSession($user, $session);

        return response()->noContent();
    }

    private function closeSession($user, $session)
    {
        // $serverhost = env('MYZAP_URL') . '/close';
        $myzapSession = $this->getMyzapSession($user, $session);
        $headers = [
            "sessionkey" => $myzapSession->session_key,
            'Content-Type' => 'application/json'
        ];

        // $response = Http::withHeaders($headers)->post($serverhost, ['session' => $session]);

        // if ($response->status() != 200) {
        //     return $response->body();
        // }

        $serverhost = env('MYZAP_URL') . '/close';
        $response = Http::withHeaders($headers)->post($serverhost, ['session' => $session]);

        return $response->body();
    }

    public function checkState(Request $request, $session)
    {
        $this->checkPerfilUsuario($request);
        $user = $request->user();
        
        $myzapSession = $this->getMyzapSession($user, $session);
        return response()->json($myzapSession);
    }

    public function sendTicket(Request $request, Sale $sale)
    {
        try {
            
            if (empty($request->input('session'))) {
                return response('Parametro session nao informado', 401);
            }
    
            $session = $request->input('session');
            $seller = $sale->vendedor;
            $sellerPhone = $seller->phone;
            $sellerPhone = preg_replace("/[^0-9]/", "", $sellerPhone);
    
            $hasText = true;
            if(!empty($request->input('hasText')) && strval($request->input('hasText')) == 'false') {
                $hasText = false;
            }
    
            if($hasText) {
                $message = $this->getDefaultText();
                $this->sendText($request->user(), $session, $sellerPhone, $message, false);
    
                if(env('APP_ENV') == 'local') {
                    $message = "http://itaimbemaquinas.com.br/wp-content/uploads/sites/79/2020/07/zap-vermelho.png"; // env('MYZAP_IMG_DIR') . $sale->billet_file;    
                } else {
                    $message = env('APP_URL') . '/assets/img/billets/' . $sale->billet_file;
                }
                
                $this->sendText($request->user(), $session, $sellerPhone, $message, true);
            } else {
                if(env('APP_ENV') == 'local') {
                    $message = "http://itaimbemaquinas.com.br/wp-content/uploads/sites/79/2020/07/zap-vermelho.png"; // env('MYZAP_IMG_DIR') . $sale->billet_file;    
                } else {
                    $message = env('APP_URL') . '/assets/img/billets/' . $sale->billet_file;
                }
                
                $this->sendText($request->user(), $session, $sellerPhone, $message, true);
            }
    
            return response("Mensagem enviada com sucesso");
        } catch(\Exception $e) {
            $response = [
                'error' => true,
                'msg' => $e->getMessage()
            ];
            
            return response()->json($response, $e->getCode());
        }
    }

    private function sendText($user, $session, $sellerPhone, $message, $isFile = false)
    {
        if (empty($message)) {
            return true;
        }

        $serverhost = env('MYZAP_URL');
        if ($isFile) {
            $serverhost .= '/sendFile';
        } else {
            $serverhost .= '/sendText';
        }

        $myzapSession = $this->getMyzapSession($user, $session);
        $headers = [
            "sessionkey" => $myzapSession->session_key,
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
            $body['caption'] = "Bilhete";
        }

        // if (strpos($sellerPhone, '2061')) {
        //     $postData = [
        //         'headers' => $headers,
        //         'body' => $body
        //     ];
        //     $this->setLog(json_encode($postData, JSON_PRETTY_PRINT));
        // }

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

    public function webhook(Request $request)
    {
        $model = new MyzapSession();
        
        try {
            $wookData = $request->all();

            // $this->setLog(json_encode($request->except('qrcode'), JSON_PRETTY_PRINT));

            if(empty($wookData['wook'])) {
                return response()->noContent();
            }

            $userId = $request->input('user') ?? null;
            $model->updateSession($wookData, $userId);

            return response('Myzap Session atualizado com sucesso!');

        } catch (\Throwable $th) {
            return response($th->getMessage(), 500);
        }
    }

    private function getDefaultText()
    {
        $text = <<<EOF
*Caro benfeitor*,

Somos gratos pelo gesto de estender a m??o ao pr??ximo e nos auxiliar a melhor atender todas as pessoas que vem at?? n??s em busca de luz, de amor, de uma palavra amiga!
*Que Deus multiplique as ben????os em sua vida!*

O resultado do sorteio dos 3 pr??mios ser?? divulgado em nosso *site* e no nosso *Instagram* no dia 17 abril de 2022.

Nos acompanhe no Insta: instagram.com/ocantosabia
E em nosso site: ocantodosabia.com.br

*Equipe de Promo????es*
EOF;

        return $text;
    }
}
