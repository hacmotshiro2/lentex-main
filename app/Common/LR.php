<?php
namespace App\Common;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

use App\Consts\MessageConst;
use App\Models\Student;
use App\Models\LineUser;
use App\Models\EntexHistory;


class LR
{
    // public static function GetLRs(){
    // //エルサポのAPIを呼び出し、LR一覧を取得
    //     // POST https://~/api/getlrs
    //     $url = env('LSUPPO_ROUTEURL')."/getlrs";

    //     // ストリームコンテキストのオプションを作成
    //     $requestData=[];
    //     $options = array(
    //         // HTTPコンテキストオプションをセット
    //         'http' => array(
    //             'method'=> 'POST',
    //             'header'=> 'Content-type: application/json; charset=UTF-8', //JSON形式で表示
    //             'content'=> $requestData
    //         )
    //     );
    //     // ストリームコンテキストの作成
    //     $context = stream_context_create($options);
    //     try{
    //         $raw_data = file_get_contents($url, false,$context);
    //         if($raw_data==false){
    //             abort(500,MessageConst::CANT_GET_LR);
    //         }
    //     }
    //     catch(Exception $ex){
    //         abort(500,MessageConst::CANT_GET_LR);
    //     }

    //     // $lrs= json_decode('[{"LearningRoomCd":"100001","LearningRoomName":"\u7389\u9020\u672c\u6821","UpdateGamen":"seeder","UpdateSystem":"lsuppo","created_at":null,"updated_at":null,"deleted_at":null},{"LearningRoomCd":"999999","LearningRoomName":"\u30c6\u30b9\u30c8LR","UpdateGamen":"manual","UpdateSystem":"manual","created_at":null,"updated_at":null,"deleted_at":null}]',true);

    //     // var_dump($raw_data);
    //     // var_dump(json_decode($raw_data,true));
    //     // echo json_last_error(); //①
    //     // echo json_last_error_msg();  
    //     // 結局エルサポ側の問題だった

    //     $lrs = json_decode($raw_data,true);

    //     return $lrs;

    // }


    public static function GetLRs(): array
    {
        $base = env('LSUPPO_ROUTEURL'); // 例: https://lsuppo.manabiail-steam.com/api
        if (empty($base)) {
            throw new Exception('LSUPPO_ROUTEURL is not set');
        }
        $url = rtrim($base, '/') . '/getlrs';

        // 認証が必要なら withToken(env('LSUPPO_API_TOKEN')) を追加
        $res = Http::timeout(5)
            ->acceptJson()
            ->asJson()
            ->post($url, []); // 必要ならPOSTボディを配列で

        $res->throw();              // 4xx/5xxは例外
        $data = $res->json();

        if (!is_array($data)) {
            throw new Exception('Invalid JSON from getlrs');
        }
        return $data;
    }
}