<div class="container mx-auto p-6 lg:flex ">
    <div class="m-4">
        <input type="text" wire:model.live="session_idc">
        
        <!-- Student List -->
        @if($plan2attends)
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-3">出席予定の生徒</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($plan2attends as $student)
                <form>
                    <input type="hidden" id="lrcd" value="$student->LearningRoomCd">
                    <input type="hidden" id="student_id" value="$student->id">
                    <button wire:click="processStudent({{ $student->id }})" 
                    class="bg-blue-500 text-white text-lg lg:text-2xl font-semibold px-6 rounded-lg hover:bg-blue-600 h-24 w-28 md:h-32 md:w-48">
                        {{$student->student->appDispName}}
                    </button>
                </endform>
                @endforeach
            </div>
        </div>

        <!-- Additional Students Section -->
        <div x-data="{ showExtra: false, extraStudents: @entangle('extraStudents'), offset: 0 }" class="mb-6">
            <a href="javascript:void(0);" @click="showExtra = !showExtra" class="text-blue-500 hover:underline">
                もし上記にない場合こちら
            </a>
            <div x-show="showExtra" class="mt-4 transition-all duration-500">
                <template x-for="student in extraStudents" :key="student.id">
                    <div class="bg-gray-100 p-4 rounded-lg mb-2">
                        <span class="text-gray-800 font-semibold" x-text="student.appDispName"></span>
                    </div>
                </template>
                <button @click="$wire.loadExtraStudents()" 
                    class="bg-gray-300 text-gray-800 px-4 py-2 rounded mt-2 hover:bg-gray-400">
                    さらに読み込む
                </button>
            </div>
        </div>
        @endif
    </form>
</div>
