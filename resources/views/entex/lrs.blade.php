@extends('layouts.lentex-base')

@section('title')
LR選択
@endsection
      
@section('contents')
<div class="bg-white py-6 sm:py-8 lg:py-12">
    <div class="max-w-screen-2xl px-4 md:px-8 mx-auto">
      <h2 class="text-gray-800 text-2xl lg:text-3xl font-bold text-center mb-8 md:mb-12">Learning Room 一覧</h2>
  
        <div class="flex flex-wrap gap-4 md:gap-6 justify-center">
          @foreach ($lrs as $lr)
          <!-- LR start -->
            <div>
            <a href="/entex/students?lrcd={{$lr["LearningRoomCd"]}}" class="group h-96 flex items-end bg-gray-100 rounded-lg overflow-hidden shadow-lg relative p-4">
              <img src="/images/159257643_resized.jpeg" loading="lazy" alt="Photo by Austin Wade" class="w-full h-full object-cover object-center absolute inset-0 group-hover:scale-110 transition duration-200" />
    
              <div class="w-full flex flex-col bg-white text-center rounded-lg relative p-20">
                {{-- <span class="text-gray-500">{{$lr->LearningRoomCd}}</span> --}}
                <span class="text-gray-500">{{$lr["LearningRoomCd"]}}</span>
                {{-- <span class="text-gray-800 text-lg lg:text-xl font-bold">{{$lr->LearningRoomName}}</span> --}}
                <span class="text-gray-800 text-lg lg:text-xl font-bold">{{$lr["LearningRoomName"]}}</span>
              </div>
            </a>
            </div>
          <!-- LR - end -->
          @endforeach
        </div>
    </div>
</div>
@endsection