<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot;

use App\Models\Student;
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
        /* Ref:Webhookイベントオブジェクト
            https://developers.line.biz/ja/reference/messaging-api/#webhook-event-objects 
        */

        //チャネルアクセストークンをセット
        $httpClient = new CurlHTTPClient(env('LINE_CHANEL_A_TOKEN'));
        //チャネルシークレットをセット
        $bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANEL_SECRET')]);
        
        $request->collect('events')->each(function($event) use($bot){

            $eventType = $event['type'];
            $messageType = $event['message']['type'];
            $replyToken = $event['replyToken'];


            //イベントがメッセージ以外だったら終了
            if($eventType != 'message'){
                //メッセージイベント以外は何もしない
                return '';
            }
            //メッセージが文字以外だったら終了
            if($messageType !='text'){
                $bot->replyText($replyToken,"本チャットでお答えできる内容ではないようです。\n恐れ入りますが、サポーターにお問い合わせください。");
                return '';
            }

            //テキストを取得(message-typeがtextじゃないと、セットされておらずエラーになるので、このタイミング)
            $messageText = $event['message']['text'];

            /* 1. 初期登録用 */



            /* 2.入退室履歴確認 */
            //「入退室履歴を確認する」というメッセージが届いたら
            if($messageText == env('LINE_BROWSEENTEX')){
                //返信メッセージ
                $resMessages = [];

                //プロファイルからテーブルを参照し、対象の生徒を取得する
                $lineUserId = $event['source']['userId'];
                $users = LINEUser::where('lineUserId',$lineUserId)->get();

                if(count($users)<1){
                
                    //まだ登録が済んでいない人が送ってきた場合
                    // $bot->replyText($replyToken,'初期登録がまだのようです');
                    $resMessages[]='初期登録がまだのようです';
                }
                else{
                    
                    foreach($users as $user){
                        //生徒単位のループ

                        //入退室履歴テーブルから情報を取得する
                        $litems = EntexHistory::getEntexHistory($user->student_id);
                        $student = Student::find($user->student_id);

                        $message = "";
                        if(count($litems)<= 0){
                            $message="まだ入退室履歴がありません";
                            // $bot->replyMessage($replyToken,new TextMessageBuilder($message));success
                        }
                        else{

                            // $message = $student->messageDispName."さんの入退室履歴";//NG
                            $message = ($student->messageDispName)."さんの入退室履歴\n\n";//( )がないとエラー

                            $i = 0;
                            foreach($litems as $item){
                                if($i>9){
                                    break;
                                }
                                $typeName = $item->type=='1'?'入室':'退室';
                                $message = $message.date('m/d H:i', strtotime(($item->entex_datetime)))." ".$typeName."\n";
                                
                                $i++;
                            }
                            $message=$message."※直近10件を表示しています";
                        }
                        //生徒毎に1メッセージ
                        // $bot->replyMessage($replyToken,new TextMessageBuilder($message));

                        $resMessages[]=$message;
                    }
                }
                //入退室履歴確認時のメッセージをまとめて送ります（応答は1回しかできないため）
                //TextMessageBuilderに配列を渡すと上手くいかないので
                switch(count($resMessages)){
                    case 1:
                        $bot->replyMessage($replyToken,new TextMessageBuilder($resMessages[0]));
                        break;
                    case 2:
                        $bot->replyMessage($replyToken,new TextMessageBuilder($resMessages[0],$resMessages[1]));
                        break;
                    case 3:
                        $bot->replyMessage($replyToken,new TextMessageBuilder($resMessages[0],$resMessages[1],$resMessages[2]));
                        break;
                    case 4:
                        $bot->replyMessage($replyToken,new TextMessageBuilder($resMessages[0],$resMessages[1],$resMessages[2],$resMessages[3]));
                        break;
                    default:
                        break;
                    //Warning 4人兄弟姉妹までしか想定しない
                }
                
                // 検証履歴
                // $bot->replyMessage($replyToken,new TextMessageBuilder('ここまできた')); //success
                // $bot->replyMessage($replyToken,new TextMessageBuilder("りんご",["ぶどう","みかん"]));//NG 200
                // $textMessageBuilder = new TextMessageBuilder(array("ぶどう","みかん")); //NG
                // $bot->replyMessage($replyToken,new TextMessageBuilder("ぶどう","みかん")); //success
                // $bot->replyMessage($replyToken,new TextMessageBuilder($resMessages[0],$resMessages[1])); //success

            }/* 2.入退室履歴確認 ここまで*/
            else{
                // $bot->replyText($replyToken,"本チャットでお答えできる内容ではないようです。\n恐れ入りますが、サポーターにお問い合わせください。");
                $bot->replyMessage($replyToken,new TextMessageBuilder("本チャットでお答えできる内容ではないようです。\n恐れ入りますが、サポーターにお問い合わせください。"));
            }
        });

        return ;
    }
   
}
