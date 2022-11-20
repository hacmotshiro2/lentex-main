
@extends('layouts.lentex-base')

@section('title')
生徒登録ページ
@endsection
      
@section('contents')
<div class="flex flex-wrap md:flex-nowrap">
    <div>
        {{-- @include('components.lsuppo-supermenu') --}}
    </div>
    <div class="ml-4">
        <div class="container px-5 py-2 mx-auto">
            <div class="flex flex-col sm:flex-row sm:justify-center lg:justify-start gap-2.5">
                <a href="/student/add" class="inline-block bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 focus-visible:ring ring-indigo-300 text-white text-sm md:text-base font-semibold text-center rounded-lg outline-none transition duration-100 px-8 py-3">新規登録</a>
            </div>
          </div>
        <form method="POST" action="#" class="row g-2">
            @csrf
            <div class="mb-2">
                <label for="verificationName" class="form-label">認証用生徒名　例：山田太郎</label>
                @if($mode=='edit')
                    <input type="hidden" name="id" value="{{$item->id}}" />
                    <x-lentex-input type="text" name="verificationName" value="{{$item->verificationName}}" class="form-control" required />
                @else
                    <x-lentex-input type="text" name="verificationName" value="{{old('verificationName')}}" class="form-control" required />
                @endif
            </div>
            <div class="mb-2">
                <label for="verificationCode" class="form-label">認証用コード　4桁</label>
                @if($mode=='edit')
                    <x-lentex-input type="text" name="verificationCode" value="{{$item->verificationCode}}" class="form-control" required maxlength="4" />
                @else
                    <x-lentex-input type="text" name="verificationCode" value="{{old('verificationCode')}}" class="form-control" required maxlength="4" />
                @endif
            </div>
            <div class="mb-2">
                <label for="appDispName" class="form-label">アプリ上の表示名</label>
                @if($mode=='edit')
                    <x-lentex-input type="text" name="appDispName" value="{{$item->appDispName}}" class="form-control" required  maxlength="40" />
                @else
                    <x-lentex-input type="text" name="appDispName" value="{{old('appDispName')}}" class="form-control" required  maxlength="40" />
                @endif
            </div>
            <div class="mb-2">
                <label for="messageDispName" class="form-label">メッセージの表示名</label>
                @if($mode=='edit')
                    <x-lentex-input type="text" name="messageDispName" value="{{$item->messageDispName}}" class="form-control" required  maxlength="40" />
                @else
                    <x-lentex-input type="text" name="messageDispName" value="{{old('messageDispName')}}" class="form-control" required  maxlength="40" />
                @endif
            </div>
            <div class="mb-2">
                <label for="lsuppoStudentCd" class="form-label">エルサポのStudentCd</label>
                @if($mode=='edit')
                    <x-lentex-input type="text" name="lsuppoStudentCd" value="{{$item->lsuppoStudentCd}}" class="form-control" maxlength="8" />
                @else
                    <x-lentex-input type="text" name="lsuppoStudentCd" value="{{old('lsuppoStudentCd')}}" class="form-control" maxlength="8" />
                @endif
            </div>
            <div class="mb-2">
                @if($mode=='edit')
                    <div class="flex justify-between">
                        <x-lentex-submit formaction="/student/edit" :mode="'edit'">更新</x-lentex-submit>
                        <x-lentex-submit formaction="/student/delete" :mode="'delete'">削除</x-lentex-submit>
                    </div>
                @elseif($mode=='add')
                    <x-lentex-submit formaction="/student/create" :mode="'add'">登録</x-lentex-submit>
                @else
                @endif
            </div>
        </form>
        <div id='list'>
            <table class="table table-striped table table-hover table table-responsive">       
                <tr>
                    <th>id</th>
                    <th>verificationName</th>
                    <th>verificationCode</th>
                    <th>appDispName</th>
                    <th>messageDispName</th>
                    <th>lsuppoStudentCd</th>
                    <th>created_at</th>
                    <th>updated_at</th>
                    <th>deleted_at</th>
                </tr>
                @foreach($items as $item)
                <tr>
                    <td><a href="/student/add/?id={{$item->id}}" class="text-indigo-700"> << {{$item->id}} >> </a></td>
                    <td>{{$item->verificationName}}</td>
                    <td>{{$item->verificationCode}}</td>
                    <td>{{$item->appDispName}}</td>
                    <td>{{$item->messageDispName}}</td>
                    <td>{{$item->lsuppoStudentCd}}</td>
                    <td>{{$item->created_at}}</td>
                    <td>{{$item->updated_at}}</td>
                    <td>{{$item->deleted_at}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection