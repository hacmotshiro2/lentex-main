<div class="p-6 bg-white shadow-lg rounded-lg">

    <!-- 戻るボタン -->
    <div class="mb-6">
        <a href="{{ route('sessions.create') }}" 
           class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200 ease-in-out flex items-center">            <!-- 左向き矢印 -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l-7-7 7-7"></path>
            </svg>
            <span>セッション一覧に戻る</span>
        </a>
    </div>
    <!-- セッション情報の表示 -->
    <div class="mb-6">
        <h3 class="font-semibold text-xl text-gray-800">セッション情報</h3>
        <div class="mt-2">
            <p class="text-gray-600 text-sm"><strong>セッションID:</strong> {{ $session_id }}</p>
            <p class="text-gray-600 font-bold md:text-2xl"><strong>コース:</strong> {{ $session->course->courseName }}</p>
            <p class="text-gray-600"><strong>開始日時:</strong> {{ \Carbon\Carbon::parse($session->sessionStartTime)->format('m/d H:i') }}</p>
            <p class="text-gray-600"><strong>終了日時:</strong> {{ \Carbon\Carbon::parse($session->sessionEndTime)->format('m/d H:i') }}</p>
        </div>
    </div>

    <h3 class="font-semibold text-xl text-gray-800">セッション出席予定を登録</h3>

    <!-- チェックボックスで生徒を選択 -->
    <form wire:submit.prevent="registerAttend" class="space-y-4 mt-6">
        <div class="space-y-4">
            <h4 class="font-medium text-lg text-gray-700">出席予定の生徒を選んで登録ボタンを押してください</h4>

            <!-- 生徒選択リスト -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($students as $student)
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="selectedStudents" value="{{ $student->id }}"
                               id="student-{{ $student->id }}" class="form-checkbox h-5 w-5 text-blue-600 border-gray-300 rounded-md">
                        <label for="student-{{ $student->id }}" class="text-sm text-gray-700">{{ $student->appDispName }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 登録ボタン -->
        <div class="mt-6">
            <button type="submit" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                登録
            </button>
        </div>
    </form>
    <!-- メッセージ表示 -->
    @if(session()->has('message'))
        <div class="mt-4 p-3 bg-green-100 text-green-800 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <!-- 出席予定の生徒リスト -->
    <div class="mt-4 bg-gray-50 p-4 border border-gray-200 rounded-lg">
        <h3 class="text-sm font-medium text-gray-700 mb-2">現在の出席予定者:</h3>
        @if (count($registeredStudents) > 0)
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($students->whereIn('id', $registeredStudents) as $student)
                <li class="text-gray-800 md:flex">
                    <div class="w-24">{{ $student->appDispName }}</div>
                    <!-- メモ入力欄 -->
                    <input 
                        type="text" 
                        wire:model="descriptions.{{ $student->id }}" 
                        placeholder="特記事項があれば入力"
                        class="text-sm text-gray-700 border rounded-md p-1 ml-2 w-64"
                    />
                    <!-- 更新ボタン -->
                    <button 
                        wire:click="updateDescription({{ $student->id }})"
                        class="ml-2 bg-green-400 hover:bg-green-600 text-white px-4 py-2 rounded-md text-xs"
                    >
                        更新
                    </button>
                </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-500">現在、登録されている出席予定者はいません。</p>
        @endif
    </div>

</div>
