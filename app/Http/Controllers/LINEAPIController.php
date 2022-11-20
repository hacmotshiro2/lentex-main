<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LINEAPIController extends Controller
{
    //
    //LINEのpushメッセージを送ります
    public static function linePushMessage(string $userId,string $message){

        //チャネルアクセストークンをセット
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(env('LINE_CHANEL_A_TOKEN'));
        //チャネルシークレットをセット
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => env('LINE_CHANEL_SECRET')]);

        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
        //第一引数は宛先のUserId #TODO
        $response = $bot->pushMessage($userId, $textMessageBuilder);

        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

        return ;

    }
}
