<div>
  <div class="relative overflow-x-auto shadow-md rounded-lg">
    <table class="min-w-full border-collapse border border-gray-300 rounded-lg shadow-md">
      <thead class="bg-gray-200">
        <tr>
            <th class="sort border p-2 border-b" wire:click="sortOrder('id')">id {!! $sortLink !!}</th>
            <th class="sort border p-2 border-b" wire:click="sortOrder('student_id')">student_id {!! $sortLink !!}</th>
            <th class="border p-2 border-b" >messageDispName</th>
            <th class="sort border p-2 border-b" wire:click="sortOrder('type')">type {!! $sortLink !!}</th>
            <th class="border p-2 border-b" >typeName</th>
            <th class="sort border p-2 border-b" wire:click="sortOrder('LearningRoomCd')">LearningRoomCd {!! $sortLink !!}</th>
            <th class="sort border p-2 border-b" wire:click="sortOrder('entex_datetime')">entex_datetime {!! $sortLink !!}</th>
            <th class="border p-2 border-b" >formattedHistory</th>
        </tr>
      </thead>
      <tbody >
        @foreach($histories as $item)
        <tr class="border-b hover:bg-gray-100">
            <td class="border-b p-2">{{$item->id}}</td>
            <td class="border-b p-2">{{$item->student_id}}</td>
            <td class="border-b p-2">{{$item->student->messageDispName}}</td>
            <td class="border-b p-2">{{$item->type}}</td>
            <td class="border-b p-2">{{$item->typeName}}</td>
            <td class="border-b p-2">{{$item->LearningRoomCd}}</td>
            <td class="border-b p-2">{{$item->entex_datetime}}</td>
            <td class="border-b p-2">{{$item->formattedHistory}}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    {{ $histories->links() }}
  </div>
</div>
