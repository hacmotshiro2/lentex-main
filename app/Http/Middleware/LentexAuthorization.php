<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LentexAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //ログイン済みでなければ、エラー
        if(!Auth::check())abort(403);

        $param = [
            'id'=>Auth::id(),
        ];
        $ua= DB::select("
        SELECT  *
        FROM m_userauthorization main
        where user_id = :id ;
        "
        ,$param);

        //m_userAuhorizationテーブルに登録がない場合はエラー
        if(count($ua)==0)abort(403);

        return $next($request);
    }
}
