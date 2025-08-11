
@extends('components.layouts.lentex-base')

@section('title')
トップメニュー
@endsection
      
@section('contents')
<div class="flex flex-wrap md:flex-nowrap">
    <div>
        <div class="rounded-lg ">
            <div class=" flex justify-center bg-white">
            <a href="{{ route('select.session') }}"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl shadow
                        bg-blue-600 text-white font-semibold
                        hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300
                        transition">
                入退室処理New
            </a>
            </div>
        </div>
    </div>
<div>
@endsection