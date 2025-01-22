<div class="container mx-auto p-2 md:p-6 ">
    <form method="GET"  
      id="sessionForm" 
      wire:ignore.self
      class="mx-auto my-2 md:my-4 max-w-2xl" style="width:75vw">
        <!-- LearningRoom Selection -->
        <div x-data="{ open: true }" class="mb-6">
            <div class="flex justify-between items-center mb-2">
                <label class="block text-lg font-semibold" for="learningRoom">ラーニングルーム選択</label>
                <button @click="open = !open" class="text-blue-500 hover:underline">
                    <span x-text="open ? 'Close' : 'Open'"></span>
                </button>
            </div>
            <div x-show="open" class="transition-all duration-500">
                <select id="learningRoom" wire:model="selectedLearningRoom" class="w-full p-3 border rounded text-sm md:text-base lg:text-2xl">
                    <option value="">-- Select a LR --</option>
                    @foreach($learningRooms as $room)
                        <option value="{{ $room['LearningRoomCd'] }}">{{ $room['LearningRoomName'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Session Selection -->
        @if($sessions)
        <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
                <label class="block text-lg font-semibold mb-2" for="session">対象のセッション</label>
                <div>
                    <!-- 「+」マークのボタン -->
                    <a href="{{ route('sessions.create') }}" class="text-blue-500 hover:underline">
                        <button class="p-2 text-xl md:text-2xl text-blue-500 rounded-full">+</button>
                    </a>
                </div>
            </div>
            <select id="session" wire:model="selectedSession" class="w-full p-3 pr-8 border rounded text-sm md:text-base lg:text-2xl">
                <option value="">-- Select a Session --</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}">
                        {{ \Carbon\Carbon::parse($session->sessionStartTime)->format('m月d 日 H:i') }} ～ 
                        <span class="font-semibold" >{{$session->course->courseName}}</span>
                    </option>
                @endforeach
            </select>
            <div class="mt-12">
                @if($selectedSession)
                <button formaction="{{ url('/select-student') . '?session_idc=' . $selectedSession }}" type="submit" id="submitButton" 
                    class="text-white text-lg lg:text-2xl font-semibold bg-blue-500 hover:bg-blue-600 px-6 rounded-lg h-8 md:h-12 w-full">
                    確定
                </button>
                @else
                <button type="submit" 
                    class="text-white text-lg lg:text-2xl font-semibold bg-gray-500 hover:bg-gray-600 px-6 rounded-lg h-8 md:h-12 w-full" disabled>
                    確定
                </button>
                @endif
            </div>
        </div>
        @endif
    </form>
</div>
