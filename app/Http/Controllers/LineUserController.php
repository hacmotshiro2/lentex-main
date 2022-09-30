<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\LineUser;

class LineUserController extends Controller
{
    // get index
    public function index(Request $request){

        //
        $items = LineUser::all();

        $args=[
            'items'=>$items,
        ];
        return view('lineusers.index',$args);
    }
    // post delete
    public function delete(Request $request){
        
        //クエリ文字列からlineusersのidを取得
        $id = $request->id;
        //削除するレコードを取得
        $lu = LineUser::find($id);
        $lu->delete();

        return redirect()->route('lineu-index');
    }
}
