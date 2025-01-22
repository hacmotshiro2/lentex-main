<?php

namespace App\Http\Livewire;

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

    public $sessionId;
    protected $queryString = ['session_id'];

    public $plan2attends = [];
    public $extraStudents = [];
    public $extraStudentOffset = 0;
    public $extraStudentLimit = 20;
    public $showPastSessions = false;


    public function mount($session_id = null)
    // public function mount()
    {
        Log::info('SelectStudent mount ',[$session_id]);

        // クエリ文字列からsession_idを取得
        $this->sessionId = $session_id;

        $this->plan2attends = Plan2Attend::where('session_id', $this->sessionId)->get();

        // 追加の学生リストをリセット
        $this->resetExtraStudents();
    }

    public function resetExtraStudents()
    {
        $this->extraStudentOffset = 0;
        $this->loadExtraStudents();
    }

    public function loadExtraStudents()
    {
        // セッションに関連する既出の学生IDを取得
        $existingStudentIds = $this->plan2attends->pluck('id')->toArray();

        // 既出以外の学生を取得
        $newStudents = Student::whereNotIn('id', $existingStudentIds)
            ->offset($this->extraStudentOffset)
            ->limit($this->extraStudentLimit)
            ->get(['id', 'appDispName']);

        // 追加の学生を更新
        $this->extraStudents = array_merge($this->extraStudents, $newStudents->toArray());
        $this->extraStudentOffset += $this->extraStudentLimit;
    }
    
    public function processStudent($studentId)
    {
        // 学生処理のロジック（仮）
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

    public function render()
    {
        return view('livewire.select-student',
        ['session_id'=>$this->sessionId,
        'session_idc'=>$this->session_idc]
        )
        ->layout('layouts.lentex-base', [ 
            'title' => '入退室処理',
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
}
