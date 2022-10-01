@extends('layouts.lentex-base')

@section('title')
ユーザー認証登録ページ
@endsection
      
@section('contents')
<div class="flex flex-wrap md:flex-nowrap">
    <div class="ml-4">
        <form method="POST" action="/userauth/create" class="row g-2">
            @csrf
            <div class="my-2">
                <label for="valuserId" class="form-label">user_id</label>
                <x-lentex-input type="text" id="valuserId" name="user_id" value="{{old('user_id')}}" class="form-control" required></x-lentex-input>
            </div>
            <div class="my-2">
                <label for="description" class="form-label">メモ・補足</label>
                <x-lentex-input type="text" id="description" name="description" value="{{old('description')}}" class="form-control" maxlength="255"></x-lentex-input>
            </div>
            <div class="my-2">
                <x-lentex-submit :mode="'add'">登録</x-lentex-submit>
            </div>
        </form>
        <div id='list' class="my-10">
            <h3>m_userauthorizationテーブル</h3>
            <table class="table-auto border-collapse border border-slate-500">       
                <tr>
                    <th class="border border-slate-600">id</th>
                    <th class="border border-slate-600">user_id</th>
                    <th class="border border-slate-600">description</th>
                    <th class="border border-slate-600">created_at</th>
                    <th class="border border-slate-600">updated_at</th>
                    <th class="border border-slate-600">deleted_at</th>
                    <th class="border border-slate-600">◆</th>
                </tr>
                @foreach($items as $item)
                <tr>
                    <td class="border border-slate-700">{{$item->id}}</td>
                    <td class="border border-slate-700">{{$item->user_id}}</td>
                    <td class="border border-slate-700">{{$item->description}}</td>
                    <td class="border border-slate-700">{{$item->created_at}}</td>
                    <td class="border border-slate-700">{{$item->updated_at}}</td>
                    <td class="border border-slate-700">{{$item->deleted_at}}</td>
                    <td class="border border-slate-700"><a href="/userauth/delete/?id={{$item->id}}"><div class="inline-block items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150"> 削除</div></td>
                </tr>
                @endforeach
            </table>
        </div>
        <div id='userList' class="my-10">
            <h3>usersテーブル</h3>
            <table class="table table-striped table table-hover table table-responsive">       
                <tr>
                    <th class="border border-slate-600">id</th>
                    <th class="border border-slate-600">name</th>
                    <th class="border border-slate-600">email</th>
                    <th class="border border-slate-600">email_verified_at</th>
                    <th class="border border-slate-600">created_at</th>
                    <th class="border border-slate-600">updated_at</th>
                    <th class="border border-slate-600">deleted_at</th>
                </tr>
                @foreach($itemsUser as $item)
                <tr>
                    <td class="border border-slate-700">{{$item->id}}</td>
                    <td class="border border-slate-700">{{$item->name}}</td>
                    <td class="border border-slate-700">{{$item->email}}</td>
                    <td class="border border-slate-700">{{$item->email_verified_at}}</td>
                    <td class="border border-slate-700">{{$item->created_at}}</td>
                    <td class="border border-slate-700">{{$item->updated_at}}</td>
                    <td class="border border-slate-700">{{$item->deleted_at}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection