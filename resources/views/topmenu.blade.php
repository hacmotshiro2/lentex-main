
@extends('layouts.lentex-base')

@section('title')
トップメニュー
@endsection
      
@section('contents')
<div class="flex flex-wrap md:flex-nowrap">
    <div>
        <div class="bg-emerald-100 rounded-lg border-y-lime-700">
            <h1>super menu</h1>
            <ul class="list-decimal my-8">
                <!-- <li><a href="/lr/add/">ラーニングルーム登録</a></li> -->
                <li class="pl-4">ラーニングルーム登録</li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href="/hogosha/add/">保護者登録</a></li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href="/student/add/">生徒登録</a></li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href="/user2hogosha/add/">ユーザーと保護者の紐づけ登録</a></li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href="/supporter/add/">サポーター登録</a></li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href="/user2suppo/add/">ユーザーとサポーターの紐づけ登録</a></li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href="/lc/list/">エルコイン登録</a></li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href="/lcziyuu/add/">エルコイン事由マスタメンテ</a></li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href="/conv/upload/">CLOVAアップロード</a></li>
                <li class="hover:bg-emerald-200 rounded pl-4"><a href="/conv/">登録済みCLOVA一覧</a></li>
            </ul>
        </div>
    </div>
<div>
@endsection