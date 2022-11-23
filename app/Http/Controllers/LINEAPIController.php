<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot;

use App\Models\LineUser;
use App\Models\EntexHistory;

class LINEAPIController extends Controller
{
    //
    //LINEのpushメッセージを送ります
    public static function linePushMessage(string $userId,string $message){

        //チャネルアクセストークンをセット
        $httpClient = new CurlHTTPClient(env('LINE_CHANEL_A_TOKEN'));
        //チャネルシークレットをセット
        $bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANEL_SECRET')]);

        $textMessageBuilder = new TextMessageBuilder($message);
        //第一引数は宛先のUserId #TODO
        $response = $bot->pushMessage($userId, $textMessageBuilder);

        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

        return ;

    }
    //Webhook LINEのあらゆるイベントの入口
    public static function lineWebhook(Request $request){

        //チャネルアクセストークンをセット
        $httpClient = new CurlHTTPClient(env('LINE_CHANEL_A_TOKEN'));
        //チャネルシークレットをセット
        $bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANEL_SECRET')]);
        
        $request->collect('events')->each(function($event) use($bot){

            //イベントがテキストメッセージの送信だったら

            //入退室履歴の確認というメッセージが届いたら
            env('LINE_BROWSEENTEX');
            
            //プロファイルからテーブルを参照し、対象の生徒を取得する
            $lineUserId = '';
            $not = LINEUser::where('lineUserId',$lineUserId);
            if(empty($not)){
                //まだ登録が済んでいない人が送ってきた場合
                $bot->replyText($event['reply-Token'],'入退室履歴がありません');
            }
            else{
                //入退室履歴テーブルから情報を取得する
                $litems = Entex::getEntexHistory($not->student_id);

                $message = "";
                if(count($litems)<=0){
                    $message="まだ入退室履歴がありません";
                }
                //最大10件まで出力
                for($i = 0; $i<10 or $i<count($litems); $i++){
                    $typeName = $litems[$i]['type']=='1'?'入室':'退室';
                    $message += $litems[$i]['entex_datetime']." ".$typeName."\n";
                }

                $bot->replyText($event['reply-Token'],$message);

            }
        });

        return 'ok';
    }
}
