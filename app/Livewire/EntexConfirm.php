<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Consts\MessageConst;
use App\Models\Student;
use App\Models\LineUser;
use App\Models\EntexHistory;
use App\Http\Controllers\LINEAPIController;


class EntexConfirm extends Component
{
    //前のページから引き継いだ情報
    //lrcd,Studentマスタのidを受け取って、入退室選択画面に遷移する
    public $lrcd = "";
    public $student_id = "";
    public $student_name = "";
    public $session_idc = "";

    public $errMessage="";

    public function mount(Request $request){

        //lrcd,Studentマスタのidを受け取って、入退室選択画面に遷移する
        $this->lrcd = $request->lrcd;
        $this->student_id = $request->student_id;
        $this->student_name = $request->student_name;
        $this->session_idc = $request->session_idc;
    }
    public function render()
    {
        return view('livewire.entex-confirm',[
            "session_idc"=>$this->session_idc,
        ])
        ->layout('components.layouts.lentex-base', [ 
            'title' => '入退室選択',
        ]);
    }

    //入室処理
    public function enter(){

        /*1分以内に入室済みの場合エラーにする*/
        if(!$this->canInsert(1)){

            $this->errMessage = "3分以内に入室処理済みです";
            return;
        }

        //生徒情報の取得
        $student = Student::find($this->student_id);
        //通知先の取得
        $nots = LineUser::where('student_id',$this->student_id)->get();
        //通知メッセージの作成
        $message = $student->messageDispName."さんは".date("H:i")."に入室しました。";

        foreach($nots as $not)
        {
            //通知
            LINEAPIController::linePushMessage($not->lineUserId,$message);

        }

        //入退室記録テーブルに更新
        $entexH = new EntexHistory();
        $entexH->student_id = $this->student_id;
        $entexH->type = 1; //1:入室
        $entexH->LearningRoomCd = $this->lrcd;
        $entexH->entex_datetime = date("Y/m/d H:i:s");

        $entexH->session_id = $this->session_idc;
        
        $entexH->save(); //INSERT

        $args=[
            'session_idc' => $this->session_idc,
        ];

        return redirect()->route('select.student',$args)->with('alertComp',MessageConst::ENT_COMPLETED);
        

    }

    //退室処理
    public function exit(){

        /*1分以内に退室済みの場合エラーにする*/
        if(!$this->canInsert(2)){

            $this->errMessage = "3分以内に退室処理済みです";
            return;
        }

        //生徒情報の取得
        $student = Student::find($this->student_id);
        //通知先の取得
        $nots = LineUser::where('student_id',$this->student_id)->get();
        //通知メッセージの作成
        $message = $student->messageDispName."さんは".date("H:i")."に退室しました。";

        foreach($nots as $not)
        {
            //通知
            LINEAPIController::linePushMessage($not->lineUserId,$message);

        }

        //入退室記録テーブルに更新
        $entexH = new EntexHistory();
        $entexH->student_id = $this->student_id;
        $entexH->type = 2; //2:退室
        $entexH->LearningRoomCd = $this->lrcd;
        $entexH->entex_datetime = date("Y/m/d H:i:s");

        $entexH->session_id = $this->session_idc;

        $entexH->save(); //INSERT
        
        $args=[
            'session_idc' => $this->session_idc,
        ];
        return redirect()->route('select.student',$args)->with('alertComp',MessageConst::EXIT_COMPLETED);
        
    }
    //入退室が連続して行われることを防ぐ
    private function canInsert($type):bool{
        //入退室履歴テーブルから情報を取得
        //1分以内に同じ更新が行われている場合、更新させない。

        //入退室記録テーブルに更新
        $entexH = EntexHistory::where('student_id',$this->student_id)
        ->where('type',$type)
        ->where('LearningRoomCd', $this->lrcd)
        ->where('entex_datetime','>=', Carbon::now()->subMinute(3))//1分以内
        ->get();

        Log::info('canInsert',[count($entexH),$this->student_id,$type,$this->lrcd,Carbon::now()->subMinute()]);

        //1件以上取得できるなら、false
        if(count($entexH)>0){
            return false;
        }
        return true;
    }


}
