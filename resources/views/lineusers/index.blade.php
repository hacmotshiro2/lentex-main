@extends('layouts.lentex-base')

@section('title')
lineusers確認ページ
@endsection
      
@section('contents')
<div class="flex flex-wrap md:flex-nowrap">
    <div class="ml-4">
        <div id='list'>
            <table class="table-auto border-collapse border border-slate-500">       
                <tr>
                    <th class="border border-slate-600">id</th>
                    <th class="border border-slate-600">student_id</th>
                    <th class="border border-slate-600">lineDisplayName</th>
                    <th class="border border-slate-600">lineUserId</th>
                    <th class="border border-slate-600">created_at</th>
                    <th class="border border-slate-600">updated_at</th>
                    <th class="border border-slate-600">deleted_at</th>
                </tr>
                @foreach($items as $item)
                <tr>
                    <td class="border border-slate-700">{{$item->id}}</td>
                    <td class="border border-slate-700">{{$item->student_id}}</td>
                    <td class="border border-slate-700">{{$item->lineDisplayName}}</td>
                    <td class="border border-slate-700">{{$item->lineUserId}}</td>
                    <td class="border border-slate-700">{{$item->created_at}}</td>
                    <td class="border border-slate-700">{{$item->updated_at}}</td>
                    <td class="border border-slate-700">{{$item->deleted_at}}</td>
                    <td class="border border-slate-700"><a href="/lineuser/delete/?id={{$item->id}}"><div class="inline-block items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150"> 削除</div></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection