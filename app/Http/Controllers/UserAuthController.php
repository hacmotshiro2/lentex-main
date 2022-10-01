<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAuthorization;
use App\Models\User;

class UserAuthController extends Controller
{
    //get userauth/add
    public function add(Request $request){
        $items = UserAuthorization::get();
        $itemsUser = User::get();


        $args = [
            'items' => $items,
            'itemsUser' => $itemsUser,
        ];

        return view('userauth.regist',$args);

    }
    //post userauth/create
    public function create(Request $request){

        $this->validate($request, UserAuthorization::$rules);
        $ua = new UserAuthorization;
        $form = $request->all();
        unset($form['_token']);
        $ua->fill($form);

        $ua->save();

        // //紐づけが完了したメールを送る
        // //管理者が登録するので、ログインユーザーではなく、いま登録したユーザーに対して送る
        // $user = User::find($u2h->user_id);
        // if(!is_null($user)){
        //     $user->notify(new User2HogoshaRegisteredNotification($user->name));
        // }

        return redirect()->route('userAuth-add');

    }
    //get userauth/delete
    public function delete(Request $request){
        $id = $request->id;

        $ua = UserAuthorization::find($id);
        $ua->delete();

        return redirect()->route('userAuth-add');
    }
}
