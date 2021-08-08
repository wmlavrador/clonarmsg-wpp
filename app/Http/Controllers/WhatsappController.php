<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    private $instanceId;
    private $token;
    private $urlBase;
    private $urlComputed;

    public function __construct()
    {
        $this->instanceId = env("CHATAPI_INSTANCE");
        $this->token = env("CHATAPI_TOKEN");
        $this->urlBase = env("CHATAPI_BASEURL");

    }

    private function compileUrl($endPoint)
    {
        return $this->urlComputed = $this->urlBase . $this->instanceId . $endPoint . "?token=" . $this->token;
    }

    public function receberMensagem(Request $request)
    {
        foreach($request["messages"] as $message){
            if($message["chatId"] == env("CHATAPI_CHATID_ORIGEM")){
                $this->replicarMensagem($message);
            }
        }
    }

    public function replicarMensagem($message)
    {
        $endpointSendMessage = $this->compileUrl("/sendMessage");
        $bodyRequest = [
            "chatId" => env("CHATAPI_CHATID_REPLICA"),
            "body" => $message["body"]
        ];

        $postMessage = Http::post($endpointSendMessage, $bodyRequest);
    }

    public function conectarCelular()
    {
        $endpointQrCode = $this->compileUrl("/qr_code");
        $endpointDialogs = $this->compileUrl("/dialogs");
        $dialogos = Http::get($endpointDialogs);

        return view('qrcode',
            [
                "qrCode" => $endpointQrCode,
                "dialogos" => $dialogos->json()
            ]
        );
    }


}
