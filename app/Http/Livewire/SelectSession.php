<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

use App\Models\Session;
use App\Models\Plan2Attend;
use App\Models\Student;

use App\Consts\MessageConst;
use App\Common\LR;


class SelectSession extends Component
{

    
    public $learningRooms = [];
    public $sessions = [];
    public $plan2attends = [];

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

    public function render()
    {
        return view('livewire.select-session')
        ->layout('layouts.lentex-base', [ 
            'title' => '入退室処理', // Bladeの @yield('title') に値を渡す
        ]);
    }
}
