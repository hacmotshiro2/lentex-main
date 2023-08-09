<div>
  <div class="relative overflow-x-auto shadow-md rounded-lg">
    <table class="table w-full text-sm text-left text-gray-600">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
            <th class="sort border border-slate-600 p-4" wire:click="sortOrder('id')">id {!! $sortLink !!}</th>
            <th class="sort border border-slate-600 p-4" wire:click="sortOrder('student_id')">student_id {!! $sortLink !!}</th>
            <th class="border border-slate-600 p-4" >messageDispName</th>
            <th class="sort border border-slate-600 p-4" wire:click="sortOrder('type')">type {!! $sortLink !!}</th>
            <th class="border border-slate-600 p-4" >typeName</th>
            <th class="sort border border-slate-600 p-4" wire:click="sortOrder('LearningRoomCd')">LearningRoomCd {!! $sortLink !!}</th>
            <th class="sort border border-slate-600 p-4" wire:click="sortOrder('entex_datetime')">entex_datetime {!! $sortLink !!}</th>
            <th class="border border-slate-600 p-4" >formattedHistory</th>
        </tr>
      </thead>
      <tbody>
        @foreach($histories as $item)
        <tr class="bg-white border-b hover:bg-gray-50">
            <td class="border border-slate-700 p-2">{{$item->id}}</td>
            <td class="border border-slate-700 p-2">{{$item->student_id}}</td>
            <td class="border border-slate-700 p-2">{{$item->student->messageDispName}}</td>
            <td class="border border-slate-700 p-2">{{$item->type}}</td>
            <td class="border border-slate-700 p-2">{{$item->typeName}}</td>
            <td class="border border-slate-700 p-2">{{$item->LearningRoomCd}}</td>
            <td class="border border-slate-700 p-2">{{$item->entex_datetime}}</td>
            <td class="border border-slate-700 p-2">{{$item->formattedHistory}}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    {{ $histories->links() }}
  </div>
</div>
