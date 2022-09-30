
@extends('layouts.lentex-base')

@section('title')
トップメニュー
@endsection
      
@section('contents')
<div class="flex flex-wrap md:flex-nowrap">
    <div>
        <div class="rounded-lg ">
            <h1>super menu</h1>
            <ul class="list-decimal my-8">
                <!-- <li><a href="/lr/add/">ラーニングルーム登録</a></li> -->
                <li class="hover:bg-emerald-200 rounded pl-4"><a href={{route('student-add')}}>生徒登録</a></li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href={{route('lineu-index')}}>ライン通知先管理</a></li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href={{route('userAuth-regist')}}>ユーザー認証</a></li>
            </ul>
        </div>
    </div>
<div>
@endsection