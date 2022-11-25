<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Consts\MessageConst;
use App\Models\Student;
use App\Models\LineUser;
use App\Models\EntexHistory;
use App\Http\Controllers\LINEAPIController;

class EntexController extends Controller
{
    //GET ラーニングルーム選択画面
    public function selectLRs(Request $request){

        //エルサポのAPIを呼び出し、LR一覧を取得
        // POST https://~/api/getlrs
        $url = env('LSUPPO_ROUTEURL')."/getlrs";

        // ストリームコンテキストのオプションを作成
        $requestData=[];
        $options = array(
            // HTTPコンテキストオプションをセット
            'http' => array(
                'method'=> 'POST',
                'header'=> 'Content-type: application/json; charset=UTF-8', //JSON形式で表示
                'content'=> $requestData
            )
        );
        // ストリームコンテキストの作成
        $context = stream_context_create($options);
        try{
            $raw_data = file_get_contents($url, false,$context);
            if($raw_data==false){
                abort(500);
            }
        }
        catch(Exception $ex){
            abort(500);
        }

        // $lrs= json_decode('[{"LearningRoomCd":"100001","LearningRoomName":"\u7389\u9020\u672c\u6821","UpdateGamen":"seeder","UpdateSystem":"lsuppo","created_at":null,"updated_at":null,"deleted_at":null},{"LearningRoomCd":"999999","LearningRoomName":"\u30c6\u30b9\u30c8LR","UpdateGamen":"manual","UpdateSystem":"manual","created_at":null,"updated_at":null,"deleted_at":null}]',true);

        // var_dump($raw_data);
        // var_dump(json_decode($raw_data,true));
        // echo json_last_error(); //①
        // echo json_last_error_msg();  
        // 結局エルサポ側の問題だった

        $lrs = json_decode($raw_data,true);

        $args=[
          'lrs'=>$lrs,
        ];
        return view('entex.lrs',$args);

    }
    //POST ラーニングルームを一つ選択したとき
    public function selectStudents(Request $request){
        
        #TODO ラーニングルーム使用権限のチェック（将来的に）

        
        // POST https://~/api/getstudents
        $url = env('LSUPPO_ROUTEURL')."/getstudents";
        
        //選択したLRコードをエルサポAPIに渡す
        $lrcd = $request->lrcd;
        $requestData=["lrcd"=>$lrcd,];
        // ストリームコンテキストのオプションを作成
        $options = array(
            // HTTPコンテキストオプションをセット
            'http' => array(
                'method'=> 'POST',
                'header'=> 'Content-type: application/json; charset=UTF-8', //JSON形式で表示
                'content'=> json_encode($requestData)
            )
        );
        // var_dump($requestData);
        // ストリームコンテキストの作成
        $context = stream_context_create($options);
        try{
            $raw_data = file_get_contents($url, false,$context);
            if($raw_data==false){
                abort(500);
            }
        }
        catch(Exception $ex){
            abort(500);
        }

        //エルサポから該当のLRに所属する生徒コードの一覧を取得
        $studentCds = json_decode($raw_data,true);
        
        //取得した生徒コードの一覧からlentexのスチューデントマスタを取得する
        $students=Student::wherein('lsuppoStudentCd',$studentCds)->get();

        //入退室から呼ばれたときは完了メッセージがついているので
        $alertComp='';
        if($request->session()->has('alertComp')){
            $alertComp = $request->session()->get('alertComp');
        }

        $args=[
            'lrcd' =>$lrcd,
            'students'=>$students,
            'alertComp'=>$alertComp,
        ];

        return view('entex.students',$args);
    }
     //POST 生徒を選択したときに
     public function confirm(Request $request){
        
        //lrcd,Studentマスタのidを受け取って、入退室選択画面に遷移する
        $lrcd = $request->lrcd;
        $student_id = $request->student_id;
        $student_name = $request->student_name;

        //
        $args=[
            'lrcd'=> $lrcd,
            'student_id'=> $student_id,
            'student_name'=>$student_name,
        ];

        return view('entex.confirm',$args);
    }
    //POST 入室処理
    public function enter(Request $request){

        $lrcd = $request->lrcd;
        $student_id = $request->student_id;

        //生徒情報の取得
        $student = Student::find($student_id);
        //通知先の取得
        $nots = LineUser::where('student_id',$student_id)->get();
        //通知メッセージの作成
        $message = $student->messageDispName."さんは".date("H:i")."に入室しました。";

        foreach($nots as $not)
        {
            //通知
            LINEAPIController::linePushMessage($not->lineUserId,$message);

        }

        //入退室記録テーブルに更新
        $entexH = new EntexHistory();
        $entexH->student_id = $student_id;
        $entexH->type = 1; //1:入室
        $entexH->LearningRoomCd = $lrcd;
        $entexH->entex_datetime = date("Y/m/d H:i:s");

        $entexH->save(); //INSERT

        $args=[
            'lrcd' => $lrcd,
        ];

        return redirect()->route('entex-students',$args)->with('alertComp',MessageConst::ENT_COMPLETED);
        
    }
    //POST 退室処理
    public function exit(Request $request){
        $lrcd = $request->lrcd;
        $student_id = $request->student_id;

        //生徒情報の取得
        $student = Student::find($student_id);
        //通知先の取得
        $nots = LineUser::where('student_id',$student_id)->get();
        //通知メッセージの作成
        $message = $student->messageDispName."さんは".date("H:i")."に退室しました。";

        foreach($nots as $not)
        {
            //通知
            LINEAPIController::linePushMessage($not->lineUserId,$message);

        }

        //入退室記録テーブルに更新
        $entexH = new EntexHistory();
        $entexH->student_id = $student_id;
        $entexH->type = 2; //2:退室
        $entexH->LearningRoomCd = $lrcd;
        $entexH->entex_datetime = date("Y/m/d H:i:s");

        $entexH->save(); //INSERT
        
        $args=[
            'lrcd' => $lrcd,
        ];
        return redirect()->route('entex-students',$args)->with('alertComp',MessageConst::EXIT_COMPLETED);
        
    }

    //GET 入退室履歴ブラウズ
    public function indexEntexHistory(Request $request){

        // $items = EntexHistory::orderBy('entex_datetime','desc')->get();
        $items = EntexHistory::getEntexHistoryAll();

        $args=[
            'items' => $items,
        ];

        return view('entex.entexhistory',$args);

    }


}
