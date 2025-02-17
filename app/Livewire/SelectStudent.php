<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;

use App\Models\Session;
use App\Models\Plan2Attend;
use App\Models\Student;

use App\Consts\MessageConst;
use App\Common\LR;

class SelectStudent extends Component
{
    #[Url(keep: true)] 
    public $session_idc='';

    protected $queryString = ['session_idc'];

    public $plan2attends = [];
    public $extraStudents = [];

    public $selectedSession ="";


    public function mount()
    {
        $this->selectedSession = Session::find($this->session_idc);

        $this->plan2attends = Plan2Attend::where('session_id', $this->session_idc)->get();

        // 追加の学生リストをリセット
        $this->loadExtraStudents();
    }
    public function render()
    {
        //入退室から呼ばれたときは完了メッセージがついているので
        $alertComp='';
        if(session()->has('alertComp')){
            $alertComp = session()->get('alertComp');
        }
        
        return view('livewire.select-student',
        ['session_idc'=>$this->session_idc])
        ->layout('components.layouts.lentex-base', [ 
            'title' => '入退室処理',
            'alertComp'=>$alertComp,
        ]);
        // 既存のレイアウトを指定
    }
    //これを書かないと正しく表示されなかった。
    protected function queryString()
    {
        return [
            'session_idc' => [
                'as' => 'session_idc',
            ],
        ];
    }

    public function loadExtraStudents()
    {
        // セッションに関連する既出の学生IDを取得
        $existingStudentIds = $this->plan2attends->pluck('student_id')->toArray();

        // 既出以外の学生を取得
        $this->extraStudents = Student::whereNotIn('id', $existingStudentIds)
            ->get();

    }
    //生徒を選択した時の処理、確認画面へ遷移する。
    public function processStudent($studentId)
    {
        //lrcd,Studentマスタのidを受け取って、入退室選択画面に遷移する
        $lrcd = $this->selectedLearningRoom;
        $student_id = $studentId;
        $student_name = Student::find($studentId)->appDispName;

        //
        $args=[
            'lrcd'=> $lrcd,
            'student_id'=> $student_id,
            'student_name'=>$student_name,
        ];

        return view('entex.confirm',$args);
    }




}
