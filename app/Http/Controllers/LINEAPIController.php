<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\Response;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

use App\Models\Student;
use App\Models\LineUser;
use App\Models\EntexHistory;

use App\Common\LINEMessageBuilder;

class LINEAPIController extends Controller
{
    /* ref Sticker List : https://developers.line.biz/ja/docs/messaging-api/sticker-list/#sticker-definitions */
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

            //LINEMessageオブジェクト
            $l_message = new LINEMessageBuilder();

            //共通プロパティの取得
            $eventType = $event['type'];

            //イベントタイプ別に処理を分岐する
            if($eventType=='postback'){

                $replyToken = $event['replyToken'];
                $data = $event['postback']['data'];
                Log::info('postbackイベントのデータです',[$data,$replyToken]);

                //ポストバックの内容によって処理を分岐する
                if($data == LINEMessageBuilder::PB_INDI_NEWREGISTRATION){

                    //返答メッセージを送る
                    $res= $bot->replyMessage($replyToken,$l_message->introduceRegistration());
                    
                    Log::info('postbackへの返答結果',[$res,$res->getRawBody()]);

                }
                return '';
            }
            else if($eventType=='message'){
                /*messageタイプの場合の処理*/
                $replyToken = $event['replyToken'];
                $messageType = $event['message']['type'];
                
                //メッセージが文字以外だったら終了
                if($messageType !='text'){
                    $bot->replyMessage($replyToken,$l_message->nonApplicable());
                    return '';
                }

                //テキストを取得(message-typeがtextじゃないと、セットされておらずエラーになるので、このタイミング)
                $messageText = $event['message']['text'];

                /* 1. 初期登録用 */
                // 山田太郎-1234 
                // -で分割して、右4桁が数字がどうかで判定します
                $splited = explode('-',$messageText);
                if(count($splited)==2 and is_numeric($splited[1])){
                    Log::Info('lineWebhookの初期登録処理が開始されました',[$messageText]);

                    //名称、識別コードとStudentマスタの突合を行う
                    $student = Student::where('verificationName',$splited[0])->where('verificationCode',$splited[1])->first();
                    
                    /* Studentと突合出来なかった場合 */
                    if(empty($student)){
                        Log::Info('初期登録処理でStudentマスタと一致しませんでした',[$messageText]);

                        $bot->replyMessage($replyToken,$l_message->notMatch());

                    }
                    /* Studentと突合できた場合 */
                    else{
                        Log::Info('初期登録処理でStudentマスタと一致しました',[$messageText]);

                        //UserIdの取得
                        $userId = $event['source']['userId'];
                        
                        //すでに登録されているかのチェック
                        $lineuser = LineUser::where('lineUserId',$userId)->where('student_id',$student->id)->first();
                        
                        /* すでに登録されている場合 */
                        if(!empty($lineuser)){
                            Log::Info('すでに登録されていました',[$messageText]);

                            $bot->replyMessage($replyToken,$l_message->alreadyExists());
        
                        }
                        /* 登録がまだの場合 */
                        else{
                            Log::Info('ユーザ登録処理を開始します',[$messageText]);
                            //LINEプロファイル情報の取得
                            $profileRes = $bot->getProfile($userId);
                            $displayName='';
                            if($profileRes->isSucceeded()){

                                $profile = $profileRes->getJSONDecodedBody();
                                $displayName = $profile['displayName'];

                                //ユーザー登録処理
                                $lineUser = new LineUser();
                                $lineUser->student_id = $student->id;
                                $lineUser->lineDisplayName = $displayName;
                                $lineUser->lineUserId = $userId;

                                $lineUser->save();

                                Log::Info('ユーザ登録処理が完了',[$lineUser]);

                                //登録完了メッセージを送る
                                $bot->replyMessage($replyToken,$l_message->registerCompleted());
                            }
                            else{
                                //ここにくる想定はないがなんらかの理由によりプロファイル情報が取得できなかった場合
                                Log::Info('LINEプロファイルが取得できませんでした',[$messageText,$lineUser]);

                                $bot->replyMessage($replyToken,$l_message->registerCanceled());

                            }

                        }
                    }

                } /* 1. 初期登録用 ここまで *

                /* 2.入退室履歴確認 */
                //「入退室履歴を確認する」というメッセージが届いたら
                else if($messageText == env('LINE_BROWSEENTEX')){
                    //返信メッセージ
                    $resMessages = [];

                    //プロファイルからテーブルを参照し、対象の生徒を取得する
                    $lineUserId = $event['source']['userId'];
                    $users = LINEUser::where('lineUserId',$lineUserId)->get();

                    if(count($users)<1){
                    
                        //まだ登録が済んでいない人が送ってきた場合
                        $resMessages[]='初期登録がまだのようです';
                        $bot->replyMessage($replyToken,$l_message->notRegistered());

                        return;
                    }
                    else{
                        $bot->replyMessage($replyToken,$l_message->entexHistory($users));  
                        return;
                    }
                    // //入退室履歴確認時のメッセージをまとめて送ります（応答は1回しかできないため）
                    // //TextMessageBuilderに配列を渡すと上手くいかないので
                    // switch(count($resMessages)){
                    //     case 1:
                    //         $bot->replyMessage($replyToken,new TextMessageBuilder($resMessages[0]));
                    //         break;
                    //     case 2:
                    //         $bot->replyMessage($replyToken,new TextMessageBuilder($resMessages[0],$resMessages[1]));
                    //         break;
                    //     case 3:
                    //         $bot->replyMessage($replyToken,new TextMessageBuilder($resMessages[0],$resMessages[1],$resMessages[2]));
                    //         break;
                    //     case 4:
                    //         $bot->replyMessage($replyToken,new TextMessageBuilder($resMessages[0],$resMessages[1],$resMessages[2],$resMessages[3]));
                    //         break;
                    //     default:
                    //         break;
                    //     //Warning 4人兄弟姉妹までしか想定しない
                    // }
                    
                    // 検証履歴
                    // $bot->replyMessage($replyToken,new TextMessageBuilder('ここまできた')); //success
                    // $bot->replyMessage($replyToken,new TextMessageBuilder("りんご",["ぶどう","みかん"]));//NG 200
                    // $textMessageBuilder = new TextMessageBuilder(array("ぶどう","みかん")); //NG
                    // $bot->replyMessage($replyToken,new TextMessageBuilder("ぶどう","みかん")); //success
                    // $bot->replyMessage($replyToken,new TextMessageBuilder($resMessages[0],$resMessages[1])); //success

                }/* 2.入退室履歴確認 ここまで*/
                else{
                    // $bot->replyText($replyToken,"本チャットでお答えできる内容ではないようです。\n恐れ入りますが、サポーターにお問い合わせください。");
                    // $bot->replyMessage($replyToken,new TextMessageBuilder("本チャットでお答えできる内容ではないようです。\n恐れ入りますが、サポーターにお問い合わせください。"));
                    $bot->replyMessage($replyToken,$l_message->nonApplicable());
                }
            }
            //イベントがpostback メッセージ以外だったら終了
            else{
                Log::Info('message以外のイベントでした',[$eventType]);
                //メッセージイベント以外は何もしない
                return '';
            }


        });

        return ;
    }
    

   
}
