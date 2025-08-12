<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

use App\Common\LR; // ← 既存のLRクラス（GetLRs()で利用）
use App\Consts\MessageConst;
use App\Models\Student;
use App\Models\LineUser;
use App\Models\EntexHistory;
use App\Http\Controllers\LINEAPIController;

class EntexController extends Controller
{

    private const TYPE_ENTER = 1;
    private const TYPE_EXIT  = 2;

    // //GET ラーニングルーム選択画面
    // public function selectLRs(Request $request){

    //     //エルサポのAPIを呼び出し、LR一覧を取得
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
    //             abort(500);
    //         }
    //     }
    //     catch(Exception $ex){
    //         abort(500);
    //     }

    //     // $lrs= json_decode('[{"LearningRoomCd":"100001","LearningRoomName":"\u7389\u9020\u672c\u6821","UpdateGamen":"seeder","UpdateSystem":"lsuppo","created_at":null,"updated_at":null,"deleted_at":null},{"LearningRoomCd":"999999","LearningRoomName":"\u30c6\u30b9\u30c8LR","UpdateGamen":"manual","UpdateSystem":"manual","created_at":null,"updated_at":null,"deleted_at":null}]',true);

    //     // var_dump($raw_data);
    //     // var_dump(json_decode($raw_data,true));
    //     // echo json_last_error(); //①
    //     // echo json_last_error_msg();  
    //     // 結局エルサポ側の問題だった

    //     $lrs = json_decode($raw_data,true);

    //     $args=[
    //       'lrs'=>$lrs,
    //     ];
    //     return view('entex.lrs',$args);

    // }

    // GET: ラーニングルーム選択画面
    public function selectLRs(Request $request)
    {
        // 可能なら共通LRクラスを使用（内部で Http ファサード化済みが理想）
        $lrs = LR::GetLRs();

        return view('entex.lrs', ['lrs' => $lrs]);
    }
    // //POST ラーニングルームを一つ選択したとき
    // public function selectStudents(Request $request){
        
    //     #TODO ラーニングルーム使用権限のチェック（将来的に）

        
    //     // POST https://~/api/getstudents
    //     $url = env('LSUPPO_ROUTEURL')."/getstudents";
        
    //     //選択したLRコードをエルサポAPIに渡す
    //     $lrcd = $request->lrcd;
    //     $requestData=["lrcd"=>$lrcd,];
    //     // ストリームコンテキストのオプションを作成
    //     $options = array(
    //         // HTTPコンテキストオプションをセット
    //         'http' => array(
    //             'method'=> 'POST',
    //             'header'=> 'Content-type: application/json; charset=UTF-8', //JSON形式で表示
    //             'content'=> json_encode($requestData)
    //         )
    //     );
    //     // var_dump($requestData);
    //     // ストリームコンテキストの作成
    //     $context = stream_context_create($options);
    //     try{
    //         $raw_data = file_get_contents($url, false,$context);
    //         if($raw_data==false){
    //             abort(500);
    //         }
    //     }
    //     catch(Exception $ex){
    //         abort(500);
    //     }

    //     //エルサポから該当のLRに所属する生徒コードの一覧を取得
    //     $studentCds = json_decode($raw_data,true);
        
    //     //取得した生徒コードの一覧からlentexのスチューデントマスタを取得する
    //     $students=Student::wherein('lsuppoStudentCd',$studentCds)->get();

    //     //入退室から呼ばれたときは完了メッセージがついているので
    //     $alertComp='';
    //     if($request->session()->has('alertComp')){
    //         $alertComp = $request->session()->get('alertComp');
    //     }

    //     $args=[
    //         'lrcd' =>$lrcd,
    //         'students'=>$students,
    //         'alertComp'=>$alertComp,
    //     ];

    //     return view('entex.students',$args);
    // }

    // POST: ラーニングルームを一つ選択
    public function selectStudents(Request $request)
    {
        // 入力チェック（最低限）
        $validated = $request->validate([
            'lrcd' => ['required', 'string', 'max:20'],
        ]);
        $lrcd = $validated['lrcd'];

        // エルサポAPI: 該当LRに所属する生徒コード一覧を取得
        $studentCds = $this->callLsuppo('/getstudents', ['lrcd' => $lrcd]);

        // Student マスタ取得（必要なカラムだけ & 名前順など任意）
        // $students = Student::whereIn('lsuppoStudentCd', $studentCds)
        //     ->orderBy('messageDispName')
        //     ->get(['id', 'lsuppoStudentCd', 'messageDispName']);
        $students=Student::wherein('lsuppoStudentCd',$studentCds)->get();

        // “メッセージ”は取り出したら消す（pull）
        $alertComp = $request->session()->pull('alertComp', '');
        $alertErr = $request->session()->pull('alertErr', '');

        return view('entex.students', [
            'lrcd'      => $lrcd,
            'students'  => $students,
            'alertComp' => $alertComp,
            'alertErr' => $alertErr,
        ]);
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
    // //POST 入室処理
    // public function enter(Request $request){

    //     $lrcd = $request->lrcd;
    //     $student_id = $request->student_id;

    //     //生徒情報の取得
    //     $student = Student::find($student_id);
    //     //通知先の取得
    //     $nots = LineUser::where('student_id',$student_id)->get();
    //     //通知メッセージの作成
    //     $message = $student->messageDispName."さんは".date("H:i")."に入室しました。";

    //     foreach($nots as $not)
    //     {
    //         //通知
    //         LINEAPIController::linePushMessage($not->lineUserId,$message);

    //     }

    //     //入退室記録テーブルに更新
    //     $entexH = new EntexHistory();
    //     $entexH->student_id = $student_id;
    //     $entexH->type = 1; //1:入室
    //     $entexH->LearningRoomCd = $lrcd;
    //     $entexH->entex_datetime = date("Y/m/d H:i:s");

    //     $entexH->save(); //INSERT

    //     $args=[
    //         'lrcd' => $lrcd,
    //     ];

    //     return redirect()->route('entex-students',$args)->with('alertComp',MessageConst::ENT_COMPLETED);
        
    // }
    // //POST 退室処理
    // public function exit(Request $request){
    //     $lrcd = $request->lrcd;
    //     $student_id = $request->student_id;

    //     //生徒情報の取得
    //     $student = Student::find($student_id);
    //     //通知先の取得
    //     $nots = LineUser::where('student_id',$student_id)->get();
    //     //通知メッセージの作成
    //     $message = $student->messageDispName."さんは".date("H:i")."に退室しました。";

    //     foreach($nots as $not)
    //     {
    //         //通知
    //         LINEAPIController::linePushMessage($not->lineUserId,$message);

    //     }

    //     //入退室記録テーブルに更新
    //     $entexH = new EntexHistory();
    //     $entexH->student_id = $student_id;
    //     $entexH->type = 2; //2:退室
    //     $entexH->LearningRoomCd = $lrcd;
    //     $entexH->entex_datetime = date("Y/m/d H:i:s");

    //     $entexH->save(); //INSERT
        
    //     $args=[
    //         'lrcd' => $lrcd,
    //     ];
    //     return redirect()->route('entex-students',$args)->with('alertComp',MessageConst::EXIT_COMPLETED);
        
    // }

    //GET 入退室履歴ブラウズ
    public function indexEntexHistory(Request $request){

        // LiveWire化に伴う変更
        // // $items = EntexHistory::orderBy('entex_datetime','desc')->get();
        // $items = EntexHistory::getEntexHistoryAll();

        // $args=[
        //     'items' => $items,
        // ];

        // return view('entex.entexhistory',$args);
        return view('entex.entexhistory');

    }

    // POST 入室
    public function enter(Request $request)
    {
        return $this->handleEntex(
            $request,
            self::TYPE_ENTER,
            '入室',
            MessageConst::ENT_COMPLETED
        );
    }

    // POST 退室
    public function exit(Request $request)
    {
        return $this->handleEntex(
            $request,
            self::TYPE_EXIT,
            '退室',
            MessageConst::EXIT_COMPLETED
        );
    }

    /**
     * 共通処理本体
     */
    private function handleEntex(Request $request, int $type, string $actionLabel, string $flashMessage)
    {
        $lrcd       = $request->lrcd;
        $studentId  = $request->student_id;

        // 生徒情報
        $student = Student::findOrFail($studentId);
        $now     = now(); // アプリのtimezoneに従う

        // 処理前チェック（3分以内に同じ処理がされている場合、処理しない）
        if(!$this->canInsert($studentId, $type, $lrcd)){
            return redirect()->route('entex-students',['lrcd'=>$lrcd])
            ->with('alertErr', "3分以内に".$actionLabel."処理済みです");
        }


        // 通知
        $message = $this->buildMessage($student->messageDispName, $actionLabel, $now);
        $this->notifyLineUsers($studentId, $message);

        // 履歴保存
        $this->storeHistory($studentId, $type, $lrcd, $now);

        // 戻り
        return redirect()
            ->route('entex-students', ['lrcd' => $lrcd])
            ->with('alertComp', $flashMessage);
    }

    /**
     * 通知メッセージを生成
     */
    private function buildMessage(string $dispName, string $actionLabel, Carbon $time): string
    {
        return "{$dispName}さんは{$time->format('H:i')}に{$actionLabel}しました。";
    }

    /**
     * LINE通知を送信
     */
    private function notifyLineUsers(int $studentId, string $message): void
    {
        // IDだけ先にpluckして軽量化
        $lineUserIds = LineUser::where('student_id', $studentId)->pluck('lineUserId');

        foreach ($lineUserIds as $lineUserId) {
            \App\Http\Controllers\LINEAPIController::linePushMessage($lineUserId, $message);
        }
    }

    /**
     * 入退室履歴を保存
     */
    private function storeHistory(int $studentId, int $type, string $lrcd, Carbon $datetime): void
    {
        $entexH = new EntexHistory();
        $entexH->student_id     = $studentId;
        $entexH->type           = $type;         // 1:入室, 2:退室
        $entexH->LearningRoomCd = $lrcd;
        $entexH->entex_datetime = $datetime->format('Y-m-d H:i:s');
        $entexH->save();
    }

    //入退室が連続して行われることを防ぐ
    private function canInsert(int $studentId, int $type, string $lrcd):bool{
        //入退室履歴テーブルから情報を取得
        //1分以内に同じ更新が行われている場合、更新させない。

        //入退室記録テーブルに更新
        $entexH = EntexHistory::where('student_id',$studentId)
        ->where('type',$type)
        ->where('LearningRoomCd', $lrcd)
        ->where('entex_datetime','>=', now()->subMinute(3))//1分以内
        ->get();

        Log::info('canInsert',[count($entexH),$studentId,$type,$lrcd,now()->subMinute()]);

        //1件以上取得できるなら、false
        if(count($entexH)>0){
            return false;
        }
        return true;
    }

    /**
     * エルサポAPI呼び出し（共通化）
     * @param string $path 例: '/getstudents'
     * @param array  $payload POSTボディ
     * @return array
     */
    private function callLsuppo(string $path, array $payload = []): array
    {
        // $base = env('LSUPPO_ROUTEURL'); // 例: https://lsuppo.manabiail-steam.com/api
        $base  = config('lsuppo.route_url');
        if (empty($base)) {
            Log::error('LSUPPO_ROUTEURL is not set');
            abort(500, MessageConst::CANT_GET_LR);
        }

        $url = rtrim($base, '/') . '/' . ltrim($path, '/');

        try {
            $http = Http::timeout(8)->acceptJson()->asJson();

            // 認証が必要なら .env のトークンをヘッダへ
            if ($token = env('LSUPPO_API_TOKEN')) {
                $http = $http->withToken($token);
            }

            $res = $http->post($url, $payload);
            $res->throw();

            $data = $res->json();
            if (!is_array($data)) {
                throw new \RuntimeException('Invalid JSON structure from LSUPPO');
            }
            return $data;
        } catch (\Throwable $e) {
            Log::error('LSUPPO API error', [
                'url'     => $url,
                'payload' => $payload,
                'message' => $e->getMessage(),
            ]);
            abort(500, MessageConst::CANT_GET_LR);
        }
    }
}
