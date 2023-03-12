@extends('layouts.lentex-base')

@section('title')
生徒選択
@endsection
      
@section('contents')
<div class="bg-white py-6 sm:py-8 lg:py-12">
    <div class="max-w-screen-2xl px-4 md:px-8 mx-auto">
      <h2 class="text-gray-800 text-2xl lg:text-3xl font-bold text-center mb-8 md:mb-12">自分の名前を選んでね</h2>
  
        <div class="flex flex-wrap gap-4 md:gap-8 justify-center">
          @foreach ($students as $student)
          <!-- LR start -->
          <form method="POST" action="/entex/confirm" class="row g-2">
          @csrf
          <input type="hidden" name="lrcd" value="{{$lrcd}}"/>
          <input type="hidden" name="student_id" value="{{$student->id}}"/>
          <input type="hidden" name="student_name" value="{{$student->appDispName}}"/>
          <div>
            <button class="group w-40 h-48 flex items-end bg-indigo-700 rounded-lg overflow-hidden shadow-lg relative p-4">
              <div class="w-full flex flex-col bg-white text-center rounded-lg relative p-4">
                {{-- <span class="text-gray-500">{{$student->id}}</span> --}}
                <span class="text-gray-800 text-lg lg:text-xl font-bold">{{$student->appDispName}}</span>
              </div>
            </button>
            </div>
          </form>
          <!-- LR end -->
          @endforeach
        </div>
        <div class="w-full flex justify-center my-8">
            <a href="/entex/lrs" class="flex items-center bg-gray-400 rounded-lg overflow-hidden shadow-lg p-4">
                <span class="text-white text-lg lg:text-xl font-bold">< ラーニングルーム選択に戻る</span>
            </a>
        </div>
    </div>
</div>
@endsection