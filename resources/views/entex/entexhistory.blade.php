@extends('layouts.lentex-base')

@section('title')
入退室履歴確認ページ
@endsection
      
@section('contents')
<div class="flex flex-wrap md:flex-nowrap">
    <div class="ml-4">
        <div id='list'>
            <table class="table-auto border-collapse border border-slate-500 ">       
                <tr>
                    <th class="border border-slate-600 p-4">id</th>
                    <th class="border border-slate-600 p-4">student_id</th>
                    <th class="border border-slate-600 p-4">type</th>
                    <th class="border border-slate-600 p-4">LearningRoomCd</th>
                    <th class="border border-slate-600 p-4">entex_datetime</th>
                    <th class="border border-slate-600 p-4">created_at</th>
                    <th class="border border-slate-600 p-4">updated_at</th>
                    <th class="border border-slate-600 p-4">deleted_at</th>
                </tr>
                @foreach($items as $item)
                <tr>
                    <td class="border border-slate-700 p-2">{{$item->id}}</td>
                    <td class="border border-slate-700 p-2">{{$item->student_id}}</td>
                    <td class="border border-slate-700 p-2">{{$item->type}}</td>
                    <td class="border border-slate-700 p-2">{{$item->LearningRoomCd}}</td>
                    <td class="border border-slate-700 p-2">{{$item->entex_datetime}}</td>
                    <td class="border border-slate-700 p-2">{{$item->created_at}}</td>
                    <td class="border border-slate-700 p-2">{{$item->updated_at}}</td>
                    <td class="border border-slate-700 p-2">{{$item->deleted_at}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection