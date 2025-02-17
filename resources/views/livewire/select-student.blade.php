<div class="container mx-auto p-6 lg:flex ">
    <div class="m-4">
        <!-- 出席予定List -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-3">出席予定の生徒</h2>
            @if(count($plan2attends) > 0 )
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($plan2attends as $attend)
                <div>
                    <form wire:key = "$attend->id" action="{{Route('entex.confirm')}}" method="POST">
                        @csrf
                        <input type="hidden" name="lrcd" value="{{$selectedSession->LearningRoomCd}}">
                        <input type="hidden" name="student_id" value="{{$attend->student_id}}">
                        <input type="hidden" name="student_name" value="{{$attend->student->appDispName}}"/>
                        <input type="hidden" name="session_idc" value="{{$session_idc}}"/>
                        <button 
                        class="bg-blue-500 text-white text-lg lg:text-2xl font-semibold px-6 rounded-lg hover:bg-blue-600 h-32 w-48">
                            {{$attend->student->appDispName}}
                        </button>
                    </form> 
                </div>
                @endforeach
            </div>
            @else
            <div>
                <p class="text-md mb-3">出席予定は登録されていません</p>
            </div>
            @endif

        </div>

        <!-- 生徒マスタ Students Section -->
        <div x-data="{ showExtra: false, extraStudents: @entangle('extraStudents'), offset: 0 }" class="mb-6">
            <a href="javascript:void(0);" @click="showExtra = !showExtra" class="text-blue-500 hover:underline">
                もし上記にない場合こちら
            </a>
            <div class="mt-4 transition-all duration-500">
                @if(count($extraStudents) > 0 )
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($extraStudents as $student)
                    <div>
                        <form wire:key = "$student->id" action="{{Route('entex.confirm')}}" method="POST">
                            @csrf
                            <input type="hidden" name="lrcd" value="{{$selectedSession->LearningRoomCd}}">
                            <input type="hidden" name="student_id" value="{{$student->id}}">
                            <input type="hidden" name="student_name" value="{{$student->appDispName}}"/>
                            <input type="hidden" name="session_idc" value="{{$session_idc}}"/>
                            <button 
                            class="bg-gray-500 text-white text-lg lg:text-2xl font-semibold px-6 rounded-lg hover:bg-gray-600 h-32 w-48">
                                {{$student->appDispName}}
                            </button>
                        </form> 
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
