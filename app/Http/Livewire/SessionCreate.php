<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Models\Session;
use App\Models\MCourse;
use App\Common\LR;

class SessionCreate extends Component
{
    public $sessions;
    public $showPastSessions = false;
    public $learningRooms = [];  // LearningRoomCdの選択肢を格納する変数
    public $courses;  // コース情報を格納する変数

    public $newSession = [
        'LearningRoomCd' => '',
        'course_id' => '',
        'sessionStartTime' => '',
        'sessionEndTime' => '',
    ];

    protected $rules = [
        'newSession.LearningRoomCd' => 'required|string|max:6',
        'newSession.course_id' => 'required|integer',
        'newSession.sessionStartTime' => 'required|date',
        'newSession.sessionEndTime' => 'required|date|after_or_equal:newSession.sessionStartTime',
    ];

    public function mount()
    {
        $this->loadLearningRooms();  // APIからLearningRoomCdをロード
        $this->courses = MCourse::all();  // m_len_courseからコース情報を取得
        $this->loadSessions();
    }

    public function loadSessions()
    {
        $this->sessions = Session::with('course')
            ->when(!$this->showPastSessions, function ($query) {
                return $query->where('sessionStartTime', '>=', Carbon::today());
            })
            ->withCount('plan2attends')
            ->orderBy('sessionStartTime', 'asc') // 開始日の昇順
            ->orderBy('course_id', 'asc')       // Course_idの昇順
            ->get();
    }

    public function loadLearningRooms()
    {
        //エルサポのAPIを呼び出し、LR一覧を取得
        $lrs = LR::GetLRs();

        // Log::Info('getlrs',$lrs());

        $this->learningRooms = $lrs;  // JSONデータを配列に変換して保存
    }

    public function togglePastSessions()
    {

        $this->showPastSessions = !$this->showPastSessions;
        $this->loadSessions();
    }

    public function createSession()
    {
        // Log::Info("createSession()",$this->newSession);
            

        $this->validate();

        Session::create($this->newSession);

        $this->newSession = [
            'LearningRoomCd' => '',
            'course_id' => '',
            'sessionStartTime' => '',
            'sessionEndTime' => '',
        ];
        $this->loadSessions();
    }

    public function deleteSession($id)
    {
        Session::findOrFail($id)->delete();
        $this->loadSessions();
    }

    public function render()
    {
        return view('livewire.session-create') // Livewire用のビュー
        ->layout('layouts.lentex-base', [ // 既存のレイアウトを指定
            'title' => 'セッション登録ページ', // Bladeの @yield('title') に値を渡す
        ]);
    }
    
}
