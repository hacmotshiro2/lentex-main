<div>
    <h2 class="text-lg font-bold">セッション管理</h2>

    <!-- チェックボックス: 過去のセッションを表示 -->
    <label class="flex items-center mt-4">
        <input type="checkbox" wire:click="togglePastSessions" class="mr-2">
        過去のセッションも表示する
    </label>

    <!-- セッション一覧 -->
    <table class="min-w-full border-collapse border border-gray-300 rounded-lg shadow-md">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border-b">LRコード</th>
                <th class="p-2 border-b">コース名</th>
                <th class="p-2 border-b">開始時間</th>
                <th class="p-2 border-b">終了時間</th>
                <th class="p-2 border-b">出席予定人数</th>
                <th class="p-2 border-b">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
            <tr class="hover:bg-gray-100">
                <td class="p-2 border-b">{{ $session->LearningRoomCd }}</td>
                <td class="p-2 border-b">{{ $session->course->courseName ?? 'N/A' }}</td>
                <td class="p-2 border-b text-center">{{ \Carbon\Carbon::parse($session->sessionStartTime)->format('m月d 日 H:i') }}</td>
                <td class="p-2 border-b text-center">{{ \Carbon\Carbon::parse($session->sessionEndTime)->format('m月d日 H:i') }}</td>
                <td class="p-2 border-b text-center">{{ $session->plan2attends_count }}</td>
                <td class="p-2 border-b text-center">
                    <div class="inline-flex items-center space-x-2">
                        <!-- 削除ボタン -->
                        <button onclick="confirmDelete({{ $session->id }})" 
                                class="inline-flex items-center text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50 px-4 py-2 rounded-md"
                            >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            削除
                        </button>
                        <!-- 出席予定編集画面へのリンク -->
                        <a href="{{ route('sessions.attend-edit', $session->id) }}" class="inline-flex items-center text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50 px-4 py-2 rounded-md">
                            出席予定編集へ
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l7-7-7-7M5 19l7-7-7-7"></path>
                            </svg>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    

    <!-- 新規セッション作成 -->
    <div class="mt-6">
        <h3 class="font-semibold text-xl">新しいセッションを作成</h3>
        <form wire:submit.prevent="createSession" class="space-y-4 mt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- LearningRoomCd (APIから取得した選択肢) -->
                <div class="relative">
                    <select wire:model="newSession.LearningRoomCd" 
                            class="border p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                        <option value="" disabled selected>LearningRoomCdを選択</option>
                        @foreach($learningRooms as $room)
                            <option value="{{ $room['LearningRoomCd'] }}">{{ $room['LearningRoomCd'] }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Course ID (ドロップダウン) -->
                <div class="relative">
                    <select wire:model="newSession.course_id" 
                            class="border p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                        <option value="" disabled selected>コースを選択</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->courseName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Session Start Time -->
                <div class="relative">
                    <input type="datetime-local" wire:model="newSession.sessionStartTime" placeholder="開始時間" 
                        class="border p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                </div>

                <!-- Session End Time -->
                <div class="relative">
                    <input type="datetime-local" wire:model="newSession.sessionEndTime" placeholder="終了時間" 
                        class="border p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="inline-flex items-center justify-center text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50 px-6 py-3 rounded-md w-full mt-4">
                セッションを登録
            </button>
        </form>
    </div>
    
</div>
   