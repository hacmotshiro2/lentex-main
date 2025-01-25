<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

use App\Models\Student;  // 生徒マスタ
use App\Models\Session;  // 登録テーブル
use App\Models\Plan2Attend;  // 登録テーブル
use Illuminate\Support\Facades\DB;  // クエリ処理

class SessionAttendanceEdit extends Component
{
    //セッションの情報
    public $session;
    public $session_id;

    public $students = [];  // 生徒マスタの選択肢
    public $selectedStudents = [];  // 選択された学生
    public $registeredStudents = [];   // DBに登録済みの出席予定者
    public $descriptions = [];  // メモ


    public function mount($session_id)
    {
        // Log::info("mount", $session_id);

        $this->session_id = $session_id;

        // セッション情報を取得
        $this->session = Session::findOrFail($this->session_id);

        $this->students = Student::all();  // 生徒マスタから全員取得

        $this->loadRegisteredStudents();

        //mount時はDBの状態＝画面の状態
        $this->selectedStudents = $this->registeredStudents;
        
    }

    public function loadRegisteredStudents()
    {
        $this->registeredStudents = Plan2Attend::where('session_id', $this->session_id)
            ->pluck('student_id')
            ->toArray();  // 登録済みの学生を取得

        // 登録済みの学生に対応するメモを設定
        foreach ($this->registeredStudents as $student_id) {
            $plan = Plan2Attend::where('session_id', $this->session_id)
                                ->where('student_id', $student_id)
                                ->first();
            $this->descriptions[$student_id] = $plan->description;
        }
    }

    public function registerAttend()
    {
        DB::transaction(function(){
            // 選択された学生を登録
            if (!empty($this->selectedStudents)) {
                foreach ($this->selectedStudents as $student_id) {
                    Plan2Attend::updateOrCreate(
                        ['session_id' => $this->session_id, 'student_id' => $student_id],
                        ['description' => $this->descriptions[$student_id] ?? null]
                    );
                }
            }
            //OFFになった学生は削除
            Plan2Attend::where('session_id', $this->session_id)
            ->whereNotIn('student_id', $this->selectedStudents)
            ->delete();
        });
        
        $this->loadRegisteredStudents();
        session()->flash('message', '出席予定を登録しました');
    }
    public function updateDescription($student_id)
    {
        // メモを更新
        $description = $this->descriptions[$student_id];

        Plan2Attend::where('session_id', $this->session_id)
            ->where('student_id', $student_id)
            ->update(['description' => $description]);

        session()->flash('message', 'メモを更新しました');
    }

    public function render()
    {
        return view('livewire.session-attendance-edit') // Livewire用のビュー
        ->layout('components.layouts.lentex-base', [ // 既存のレイアウトを指定
            'title' => 'セッション出席予定登録ページ', // Bladeの @yield('title') に値を渡す
        ]);
    }
}
