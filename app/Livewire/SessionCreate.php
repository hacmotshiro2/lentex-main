<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Models\Session;
use App\Models\MCourse;
use App\Common\LR;

class SessionCreate extends Component
{
    use WithPagination;

    public $mode='create'; // create or update 

    public $sessions;
    public $showPastSessions = false;
    public $learningRooms = [];  // LearningRoomCdの選択肢を格納する変数
    public $courses;  // コース情報を格納する変数

    //編集中のレコードのid
    public $editing_id = 0;

    // フィルター用の変数
    public $filteredLRCds = '';
    public $filteredCourseNames = '';
    public $filteredDate = '';

    public $learningRoomOptions = [];
    public $courseOptions = [];

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
        'newSession.course_id' => 'required',
        'newSession.sessionStartTime' => 'required|date',
        'newSession.sessionEndTime' => 'required|date|after_or_equal:newSession.sessionStartTime',
    ];

    public function mount()
    {
        $this->mode="create";
        $this->editing_id = 0;
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

        $this->learningRooms = $lrs; 
    }

    public function loadSessions()
    {
        // 各フィルターの選択肢を取得（重複なし）
        $sessionsDraft = Session::when(!$this->showPastSessions, function ($query) {
            return $query->where('sessionStartTime', '>=', Carbon::today());
        });
        $this->learningRoomOptions = $sessionsDraft->select('LearningRoomCd')->distinct()->pluck('LearningRoomCd')->toArray();
        $this->courseOptions = MCourse::select('courseName')->distinct()->pluck('courseName')->toArray();
        
        $sessionsDraft= Session::leftJoin('m_len_course', 'r_len_session.course_id', '=', 'm_len_course.id')
            ->when(!$this->showPastSessions, function ($query) {
                return $query->where('sessionStartTime', '>=', Carbon::today());
            })
            ->withCount('plan2attends');

        // LearningRoomCdのフィルター
        if (!empty($this->filteredLRCds)) {
            $sessionsDraft->where('r_len_session.LearningRoomCd', $this->filteredLRCds);
        }
        // CourseNameのフィルター
        if (!empty($this->filteredCourseNames)) {
            $sessionsDraft->where('m_len_course.courseName', $this->filteredCourseNames);
        }
        // sessionStartTimeのフィルター（日付指定）
        if (!empty($this->filteredDate)) {
            $sessionsDraft->whereDate('r_len_session.sessionStartTime', $this->filteredDate);
        }
        $this->sessions = $sessionsDraft->orderBy('sessionStartTime', 'asc') // 開始日の昇順
            ->orderBy('course_id', 'asc')       // Course_idの昇順
            ->get();
    }

    //セッション情報作成処理
    public function createSession()
    {

        //Validation
        $this->checkInput();

        //INSERT処理
        Session::create($this->newSession);

        // 通知メッセージの表示
        session()->flash('message', 'セッションを登録しました。');

        $this->editing_id = 0;

        $this->newSession = [
            'LearningRoomCd' => '',
            'course_id' => '',
            'sessionStartTime' => '',
            'sessionEndTime' => '',
        ];
        $this->loadSessions();
    }
    //セッション情報更新処理
    public function updateSession()
    {

        //Validation
        $this->checkInput();

        //UPDATE処理
        $session = Session::findOrFail($this->editing_id);
        $session->LearningRoomCd = $this->newSession['LearningRoomCd'];
        $session->course_id = $this->newSession['course_id'];
        $session->sessionStartTime = $this->newSession['sessionStartTime'];
        $session->sessionEndTime = $this->newSession['sessionEndTime'];
        $session->save();

        // 通知メッセージの表示
        session()->flash('message', 'セッションを更新しました。');

        $this->newSession = [
            'LearningRoomCd' => '',
            'course_id' => '',
            'sessionStartTime' => '',
            'sessionEndTime' => '',
        ];
        $this->loadSessions();
    }

    // public function deleteSession($id)
    // {
    //     Session::findOrFail($id)->delete();
    //     $this->loadSessions();
    // }

    //
    private function checkInput(){

        $this->validate();

        // セッション開始と終了が12時間以上開いていればエラーとする
        $start = Carbon::parse($this->newSession['sessionStartTime']);
        $end = Carbon::parse($this->newSession['sessionEndTime']);

        //12時間以上間があいていたらエラーにする
        if ($start->diffInHours($end) > 12) {
            $this->addError('newSession.sessionEndTime', '終了時間は開始時間から12時間以内にしてください。');
            return;
        }

    }
    
    /* UI */
    //セッション開始日が変更されたとき
    //function名を命名規則に従うことで、勝手に発火される
    public function updatedNewSessionSessionStartTime($value)
    {
        // もしセッション終了日が空白なら1時間後を自動セット
        if (!empty($value) && empty($this->newSession['sessionEndTime'])) {
            $this->newSession['sessionEndTime'] = Carbon::parse($value)->addHour()->format('Y-m-d\TH:i');
        }
    }
    //フィルター条件が変更されたら
    public function updatedFilteredLRCds($value)
    {
        $this->loadSessions();
    }
    public function updatedFilteredCourseNames($value)
    {
        $this->loadSessions();
    }
    public function updatedFilteredDate($value)
    {
        $this->loadSessions();
    }

    //過去のセッションを表示するチェックボックス
    public function togglePastSessions()
    {

        $this->showPastSessions = !$this->showPastSessions;
        $this->loadSessions();
    }

    //「編集」ボタンを押したとき、編集モードで画面セットします。
    public function editSession($id)
    {
        $this->mode="update";
        $this->editing_id = $id;

        $session = Session::findOrFail($id);

        $this->newSession = [
            'LearningRoomCd' => $session->LearningRoomCd,
            'course_id' => $session->course_id,
            'sessionStartTime' => $session->sessionStartTime,
            'sessionEndTime' => $session->sessionEndTime,
        ];

    }

}
