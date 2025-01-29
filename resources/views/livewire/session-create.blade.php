<div>
    <h2 class="text-lg font-bold">セッション管理</h2>

    <!-- チェックボックス: 過去のセッションを表示 -->
    <label class="flex items-center mt-4">
        <input type="checkbox" wire:click="togglePastSessions" class="mr-2" />
        過去のセッションも表示する
    </label>

    <!-- セッション一覧 -->
    <table class="min-w-full border-collapse border border-gray-300 rounded-lg shadow-md">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border-b text-left">LRコード</th>
                <th class="p-2 border-b text-left">コース名</th>
                <th class="p-2 border-b text-left">開始時間</th>
                <th class="hidden lg:table-cell p-2 border-b text-left">終了時間</th>
                <th class="hidden lg:table-cell p-2 border-b text-right">出席予定人数</th>
                <th class="p-2 border-b text-center">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
            <tr class="hover:bg-gray-100">
                <td class="p-2 border-b text-left">{{ $session->LearningRoomCd }}</td>
                <td class="p-2 border-b text-left">{{ $session->course->courseName ?? 'N/A' }}</td>
                <td class="p-2 border-b text-left">{{ \Carbon\Carbon::parse($session->sessionStartTime)->format('m月d日 H:i') }}</td>
                <td class="hidden lg:table-cell p-2 border-b text-left">{{ \Carbon\Carbon::parse($session->sessionEndTime)->format('m月d日 H:i') }}</td>
                <td class="hidden lg:table-cell p-2 border-b text-right">{{ $session->plan2attends_count }} 人</td>
                <td class="p-2 border-b text-center">
                    <div class="inline-flex items-center space-x-2">
                        <!-- 削除ボタン -->
                        <!-- <button onclick="confirmDelete({{ $session->id }})" 
                                class="inline-flex items-center text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50 px-4 py-2 rounded-md"
                            > -->
                            <button 
                                type="button"
                                wire:click="deleteSession({{ $session->id }})" 
                                wire:confirm="{{\Carbon\Carbon::parse($session->sessionStartTime)->format('m月d日 H:i')}} のセッションを削除します。よろしいですか？"
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
        <!-- メッセージ表示 -->
        @if(session()->has('message'))
        <div class="mt-4 p-3 bg-green-100 text-green-800 rounded-md mb-4">
            {{ session('message') }}
        </div>
        @endif
        <h3 class="font-semibold text-xl">新しいセッションを作成</h3>
        <form wire:submit.prevent="createSession" class="space-y-4 mt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- LearningRoomCd (APIから取得した選択肢) -->
                <div class="relative">
                    <select wire:model.live="newSession.LearningRoomCd"
                            class="border p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                        <option value="" disabled selected>LearningRoomCdを選択</option>
                        @foreach($learningRooms as $room)
                            <option value="{{ $room['LearningRoomCd'] }}">{{ $room['LearningRoomCd'] }}</option>
                        @endforeach
                    </select>
                    @error('newSession.LearningRoomCd') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                
                <!-- Course ID -->
                <div class="relative">
                    <select wire:model.live="newSession.course_id"
                            class="border p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                        <option value="" disabled selected>コースを選択</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->courseName }}</option>
                        @endforeach
                    </select>
                    @error('newSession.course_id') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Session Start Time -->
                <div class="relative">
                    <input type="datetime-local" wire:model.live="newSession.sessionStartTime" 
                        class="border p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                    @error('newSession.sessionStartTime') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Session End Time -->
                <div class="relative">
                    <input type="datetime-local" wire:model.live="newSession.sessionEndTime"
                        class="border p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                    @error('newSession.sessionEndTime') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="inline-flex items-center justify-center text-white bg-blue-500 hover:bg-blue-600 
                focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50 px-6 py-3 rounded-md w-full mt-4">
                セッションを登録
            </button>
        </form>
    </div>
    <!-- 削除確認モーダル -->
    <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded shadow-lg text-center">
            <p class="mb-4">削除してよろしいですか？</p>
            <button id="confirm-delete" class="bg-red-500 text-white px-4 py-2 rounded mr-2">削除する</button>
            <button onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded">キャンセル</button>
        </div>
    </div>
    <script>
        let deleteId = null;

        confirmDelete = function(id) {
            deleteId = id;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        closeModal = function(){
            deleteId = null;
            document.getElementById('delete-modal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const confirmDeleteButton = document.getElementById('confirm-delete');
            if (confirmDeleteButton) {
                confirmDeleteButton.addEventListener('click', function () {
                    if (deleteId !== null) {
                        Livewire.dispatch('deleteSession', deleteId); // Livewireイベントをトリガー
                        closeModal();
                    }
                });
            }
        });
    </script>
</div>
   