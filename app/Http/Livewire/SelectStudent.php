<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

use App\Models\Session;
use App\Models\Plan2Attend;
use App\Models\Student;

use App\Consts\MessageConst;
use App\Common\LR;

class SelectStudent extends Component
{
    public $learningRooms = [];
    public $sessions = [];
    public $plan2attends = [];
    public $extraStudents = [];
    public $extraStudentOffset = 0;
    public $extraStudentLimit = 20;
    public $showPastSessions = false;

    public $selectedLearningRoom = null;
    public $selectedSession = null;

    public function mount()
    {
        //APIでエルサポからLRを取得する
        $this->learningRooms = LR::GetLRs();
    }
    //LRが選択された時の処理
    public function updatedSelectedLearningRoom($value)
    {
        $this->sessions = Session::with('course')
            ->where('LearningRoomCd', $value)
            ->when(function ($query) {
                return $query->where('sessionStartTime', '>=', Carbon::today());
            })
            ->orderBy('sessionStartTime', 'asc') // 開始日の昇順
            ->orderBy('course_id', 'asc')       // Course_idの昇順
            ->get();

        $this->selectedSession = null;
        $this->plan2attends = [];
    }
    //セッションが選択された時の処理
    public function updatedSelectedSession($value)
    {
        $this->plan2attends = Plan2Attend::where('session_id', $value)->get();

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
        session()->flash('message', "Student {$studentId} processed successfully!");
    }

    public function render()
    {
        return view('livewire.select-student')
        ->layout('layouts.lentex-base', [ 
            'title' => '入退室処理', // Bladeの @yield('title') に値を渡す
        ]);
        // 既存のレイアウトを指定
    }
}
