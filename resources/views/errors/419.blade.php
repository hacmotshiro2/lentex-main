@extends('components.layouts.lentex-base')

@section('title')
ERROR
@endsection
      
@section('contents')
<div class="bg-white py-6 sm:py-8 lg:py-12">
    <div class="max-w-screen-2xl px-4 md:px-8 mx-auto">
        <div class="flex flex-col items-center">
            <p class="text-indigo-500 text-sm md:text-base font-semibold uppercase mb-4">419</p>
            <h1 class="text-gray-800 text-2xl md:text-3xl font-bold text-center mb-2">セッションの有効期限が切れています</h1>
            <p class="max-w-screen-md text-gray-500 md:text-lg text-center mb-12">Page Expired</p>
            <a href="{{route('select.session')}}" class="inline-block bg-indigo-500 hover:bg-indigo-600 focus-visible:ring ring-indigo-400 text-white text-xl md:text-2xl font-semibold text-center rounded-md outline-none transition duration-100 px-10 py-6 mb-8">入退室処理トップ</a>
            <a href="/" class="inline-block bg-gray-200 hover:bg-gray-300 focus-visible:ring ring-indigo-300 text-gray-500 active:text-gray-700 text-sm md:text-base font-semibold text-center rounded-md outline-none transition duration-100 px-8 py-3">トップページ</a>
        </div>
    </div>
</div>
@endsection