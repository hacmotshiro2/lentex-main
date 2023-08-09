@extends('layouts.lentex-base')

@section('title')
入退室履歴確認ページ
@endsection
      
@section('contents')
<div class="flex flex-wrap md:flex-nowrap">
    <div class="ml-4">
        <div id='list'>
        </div>
        <div>
            <livewire:entex-history />
        </div>
    </div>
</div>
@endsection