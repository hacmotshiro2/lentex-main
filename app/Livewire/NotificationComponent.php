<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationComponent extends Component
{
    public $showNF = false;
    public $message = '';
    
    // イベントリスナーの定義
    protected $listeners = ['notify' => 'showNotification'];

    public function showNotification($message)
    {
        $this->message = $message;
        $this->showNF = true;
        
        // 数秒後に通知を非表示にする
        $this->dispatch('hide-notification');  // イベントをディスパッチ
    }

    public function render()
    {
        return view('livewire.notification-component');
    }
}
