<?php

namespace App\Livewire;

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

    // イベントリスナーの定義
    protected $listeners = ['deleteSession' => 'deleteSession'];

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

    public function render()
    {
        // return view('livewire.session-create') // Livewire用のビュー
        // ->layout('components.layouts.lentex-base', [ // 既存のレイアウトを指定
        //     'title' => 'セッション登録ページ', // Bladeの @yield('title') に値を渡す
        // ]);
        return view('livewire.session-create')
        ->layout('components.layouts.lentex-base')
        ->title('セッション登録ページ');
    }



    /* DB Access */

    public function loadLearningRooms()
    {
        //エルサポのAPIを呼び出し、LR一覧を取得
        $lrs = LR::GetLRs();

        // Log::Info('getlrs',$lrs());

        $this->learningRooms = $lrs;  // JSONデータを配列に変換して保存
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

    public function createSession()
    {

        $this->validate();

        // セッション開始と終了が12時間以上開いていればエラーとする
        $start = Carbon::parse($this->newSession['sessionStartTime']);
        $end = Carbon::parse($this->newSession['sessionEndTime']);

        Log::info("custome",[$start->diffInHours($end)]);

        //12時間以上間があいていたらエラーにする
        if ($start->diffInHours($end) > 12) {
            $this->addError('newSession.sessionEndTime', '終了時間は開始時間から12時間以内にしてください。');
            return;
        }

        //INSERT処理
        Session::create($this->newSession);

        // 通知メッセージの表示
        session()->flash('message', 'セッションを登録しました。');

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

    
    /* UI */
    //セッション開始日が変更されたとき
    //function名を命名規則に従うことで、勝手に発火される
    public function updatedNewSessionSessionStartTime($value)
    {
        // もしセッション終了日が空白なら1時間後を自動セット
        if (!empty($value)) {
            $this->newSession['sessionEndTime'] = Carbon::parse($value)->addHour()->format('Y-m-d\TH:i');
        }
    }

    //過去のセッションを表示するチェックボックス
    public function togglePastSessions()
    {

        $this->showPastSessions = !$this->showPastSessions;
        $this->loadSessions();
    }

}
