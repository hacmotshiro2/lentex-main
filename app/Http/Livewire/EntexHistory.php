<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EntexHistory as EH;


class EntexHistory extends Component
{
    use WithPagination;

    public $orderColumn = "entex_datetime";
    public $sortOrder = "desc";
    public $sortLink = '<i class="sorticon fa-solid fa-caret-up"></i>';

    public function render()
    {
          // $items = EntexHistory::orderBy('entex_datetime','desc')->get();
        //   $items = EntexHistory::getEntexHistoryAll();
          $items = EH::with('student')->orderby($this->orderColumn,$this->sortOrder)->paginate(30);
          $args=[
              'histories' => $items,
          ];
  
        return view('livewire.entex-history',$args);
    }

    public function updated(){
        //リフレッシュ
        $this->resetPage();
    }

    public function sortOrder($columnName=""){
        $caretOrder = "up";
        //今がASCならDESC。DESCならASC
        if($this->sortOrder == 'asc'){
             $this->sortOrder = 'desc';
             $caretOrder = "down";
        }else{
             $this->sortOrder = 'asc';
             $caretOrder = "up";
        } 
        $this->sortLink = '<i class="sorticon fa-solid fa-caret-'.$caretOrder.'"></i>';

        $this->orderColumn = $columnName;

    }
}
