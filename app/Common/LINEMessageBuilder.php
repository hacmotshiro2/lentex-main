<?php

namespace App\Common;

use Illuminate\Database\Eloquent\Collection;

use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

use App\Models\Student;
use App\Models\LineUser;
use App\Models\EntexHistory;

class LINEMessageBuilder
{
    //MessageBuilderをつくって返すクラスです

    //postback data の識別につかう
    const PB_INDI_NEWREGISTRATION = "新規登録方法を案内する";

    //新規登録の案内メッセージ
    public function introduceRegistration($fromNotRegistered = false):MessageBuilder{

        $multiMes = new MultiMessageBuilder();

        if($fromNotRegistered){
            //0つ目 入退室履歴を見ようとして、まだ未登録だった場合は、先にこのメッセージを出す
            $multiMes->add(new TextMessageBuilder("初期登録がまだのようです"));
        }
        //1つ目 説明画像
        $originalContentURL = url("images/line_message_newregister.png");
        $previewImageURL = url("images/line_message_newregister_compressed.png");
        if(env('APP_DEBUG')){
            //httpを強制的にhttpsにする
            $originalContentURL = str_replace("http://","https://",$originalContentURL);
            $previewImageURL = str_replace("http://","https://",$previewImageURL);
        }
        // $multiMes->add(new ImageMessageBuilder(url("images/line_message_newregister.png"),url("images/line_message_newregister.png")));
        $multiMes->add(new ImageMessageBuilder($originalContentURL,$previewImageURL));
        //2つ目 説明文
        $multiMes->add(new TextMessageBuilder("上図のように\n（お子様のお名前)-(識別コード)を送ってください\nお名前はフルネームスペースなし\n識別コードは生年月日の月日4桁\nでお願いします"));

        return $multiMes;
    }

    //LINEUser登録が上手くいったとき
    public function registerCompleted():MessageBuilder{

        $multiMes = new MultiMessageBuilder();
        $multiMes->add(new TextMessageBuilder("正しく登録できました。ありがとうございました。"));
        $multiMes->add(new StickerMessageBuilder('8515','16581243'));//ありがとうございました。のスタンプ

        return $multiMes;
    }
    //登録しようとしたが、名前か識別コードが一致しなかったとき
    public function notMatch():MessageBuilder{
        return new TextMessageBuilder("お子様の名前か識別コードが異なるようです。\nお子様の名前に識別コードを添えて送信してください。\n例)山田太郎-0229");
    }
    //登録しようとしたが、既に登録されていたとき
    public function alreadyExists():MessageBuilder{
        return new TextMessageBuilder("既にご登録頂いています");
    }
    //登録処理の途中でキャンセルになった（プロファイル情報の取得ができなかった）
    public function registerCanceled():MessageBuilder{

        return new TextMessageBuilder("登録処理の途中でエラーが発生しました。\n恐れ入りますが、もう一度お試し頂くかサポーターにお問い合わせ下さい。\n詳細:プロファイル情報の取得に失敗");
    }
    
    //入退室履歴を確認しようとして、初期登録がまだの場合
    public function notRegistered():MessageBuilder{
        return $this->introduceRegistration(true); 
    }
    //入退室履歴メッセージ $usersはLINEUser型である必要
    public function entexHistory(Collection $users):MessageBuilder{

        $multiMes = new MultiMessageBuilder();

        foreach($users as $user){
            //生徒単位のループ

            //入退室履歴テーブルから情報を取得する
            // 2023/08/08 Eloquent化に伴う変更
            // $litems = EntexHistory::getEntexHistory($user->student_id);
            // Student_idが一致するものを取得
            // 入退室時刻の降順で10件だけ
            $litems = EntexHistory::where('student_id',$user->student_id)->orderBy('entex_datetime','desc')->limit(10)->get();
            $student = Student::find($user->student_id);

            $message = "";
            if(count($litems)<= 0){

                $multiMes->add(new TextMessageBuilder(($student->messageDispName)."さんはまだ入退室履歴がありません"));
            
            }
            else{
                /*作成するメッセージのイメージ
                斉藤 陽翔さんの入退室履歴

                11/29 18:16 入室
                11/17 06:35 入室
                11/17 06:32 入室
                11/16 14:40 入室
                11/16 14:40 入室
                11/16 14:40 入室
                ※直近10件を表示しています

                */

                // $message = $student->messageDispName."さんの入退室履歴";//NG
                $message = ($student->messageDispName)."さんの入退室履歴\n\n";//( )がないとエラー

                // 2023/08/08 Eloquent化に伴う変更
                // $i = 0;
                // foreach($litems as $item){
                //     if($i>9){
                //         break;
                //     }
                //     // 2023/08/08 Eloquent化に伴う変更
                //     // $typeName = $item->type=='1'?'入室':'退室';
                //     $typeName = $item->type=='1'?'入室':'退室';
                //     $message = $message.date('m/d H:i', strtotime(($item->entex_datetime)))." ".$typeName."\n";
                    
                //     $i++;
                // }

                foreach($litems as $item){
                    $message = $message.$item->formattedHistory."\n";
                }

                $message=$message."※直近10件を表示しています";

                $multiMes->add(new TextMessageBuilder($message));
            }

        }

        return $multiMes;
    }
    //どの条件にも該当しなかった場合のメッセージ
    // return MessageBuilder
    public function nonApplicable():MessageBuilder{

        $multi =new MultiMessageBuilder();

        //1通目 テキストメッセージで案内
        $multi->add(new TextMessageBuilder("本チャットでお答えできる内容ではないようです。\n以下メニューから選んでください"));

        //2通目 カルーセルテンプレートメッセージで、次のアクションを提案する
        //1列目
        $imageURL = url("images/line_message_carousel_register.png");
        if(env('APP_DEBUG')){
            //httpを強制的にhttpsにする
            $imageURL = str_replace("http://","https://",$imageURL);
        }
        $action = new PostbackTemplateActionBuilder('こちらをタップ',self::PB_INDI_NEWREGISTRATION,null,null,null);
        $ccs[] = new CarouselColumnTemplateBuilder("新規登録案内","新規登録する方法を見るには下のボタンをタップしてください",$imageURL,[$action],null,null);
        
        //2 ~ (N-1)列目

        //最終列
        $imageURL = url("images/line_message_carousel_inquiry.png");
        if(env('APP_DEBUG')){
            //httpを強制的にhttpsにする
            $imageURL = str_replace("http://","https://",$imageURL);
        }
        $action = new UriTemplateActionBuilder("HPの問い合わせフォームはこちら","https://manabiail-steam.com/contact");
        $ccs[] = new CarouselColumnTemplateBuilder("お問い合わせ下さい","左記で解決できない場合は\n恐れ入りますが、サポーターに直接お問い合わせ下さい",$imageURL,[$action],null,null);

        //MessageBuilderをinterfaceとして持つ、TemplateMessageBuilderを返す
        $multi->add(new TemplateMessageBuilder("新規登録案内",new CarouselTemplateBuilder($ccs,null,null),null,null));

        return $multi;
    }
}
