@extends('components.layouts.lentex-base')

@section('title')
入退室処理
@endsection
      
@section('contents')
<div class="bg-white py-6 sm:py-8 lg:py-12">
    <div class="max-w-screen-2xl px-4 md:px-8 mx-auto">
        <h2 class="text-gray-800 text-2xl lg:text-3xl font-bold text-center mb-8 md:mb-12">{{$student_name}}　さん</h2>
        <h3 class="text-gray-800 text-2xl lg:text-3xl font-bold text-center mb-8 md:mb-12">
            <span id="clock"></span>
        </h3>
        <form method="POST">
            @csrf
            <input type="hidden" name="lrcd" value="{{$lrcd}}"/>
            <input type="hidden" name="student_id" value="{{$student_id}}"/>
            <div class="flex flex-wrap gap-4 md:gap-8 lg:gap-12 justify-center">
                    <div>
                        <button formaction="/entex/enter" class="group h-48 flex items-end bg-blue-400 rounded-lg overflow-hidden shadow-lg relative p-12">
                        <div class="w-full flex flex-col bg-white text-center rounded-lg relative p-4">
                            <span class="text-gray-800 text-lg md:text-xl font-bold">入室</span>
                        </div>
                        </button>
                    </div>
                    <div>
                        <button formaction="/entex/exit" class="group h-48 flex items-end bg-red-400 rounded-lg overflow-hidden shadow-lg relative p-12">
                        <div class="w-full flex flex-col bg-white text-center rounded-lg relative p-4">
                            <span class="text-gray-800 text-lg md:text-xl font-bold">退室</span>
                        </div>
                        </button>
                    </div>
            </div>
        </form>
        <div class="w-full flex justify-center my-8">
            <a href="/entex/students?lrcd={{$lrcd}}" class="flex items-center bg-gray-400 rounded-lg overflow-hidden shadow-lg p-4">
                <span class="text-white text-lg lg:text-xl font-bold">< 生徒選択に戻る</span>
            </a>
        </div>
    </div>
</div>
<script >
    // 時刻を表示するための関数
    function displayClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const formattedTime = `${hours}:${minutes}:${seconds}`;
        document.getElementById('clock').textContent = formattedTime;
    }

    // 1秒ごとに時刻を更新
    setInterval(displayClock, 1000);

    // ページ読み込み時に初回の表示を行う
    displayClock();
</script>
@endsection