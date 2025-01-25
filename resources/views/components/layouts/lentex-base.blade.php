<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title' , '今ついたよでたよ')</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->

        <!-- livewireで決まり文句 -->
        @livewireStyles

        <!-- Fontawesome -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

        <style type="text/css">
        .sorticon{
                visibility: hidden;
                color: darkgray;
        }
        .sort:hover .sorticon{
                visibility: visible;
        }
        .sort:hover{
                cursor: pointer;
        }
       </style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('components.layouts.lentex-navigation')

            <!-- Page Content -->
            <main class="container px-5 py-8 lg:py-12 mx-auto">
                <div>
                    @if(!empty($alertComp))
                    <x-lentex-alert-completed :alert="$alertComp" />
                    @endif
                </div>

                <!-- livewireテンプレートだけの場合はslot -->
                @if (isset($slot))
                <div class="w-full mx-auto">
                    <div class="mx-4">
                        <div id='list'>
                        </div>
                        <div>
                            {{$slot}}
                        </div>
                    </div>
                </div>
                @else
                    @yield('contents')
                @endif
            </main>
        </div>
        <!-- モーダル -->
        <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded shadow-lg text-center">
                <p class="mb-4">削除してよろしいですか？</p>
                <button id="confirm-delete" class="bg-red-500 text-white px-4 py-2 rounded mr-2">削除する</button>
                <button onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded">キャンセル</button>
            </div>
        </div>
        <script>
            let deleteId = null;

            function confirmDelete(id) {
                deleteId = id;
                document.getElementById('delete-modal').classList.remove('hidden');
            }

            function closeModal() {
                deleteId = null;
                document.getElementById('delete-modal').classList.add('hidden');
            }

            document.addEventListener('DOMContentLoaded', () => {
                const confirmDeleteButton = document.getElementById('confirm-delete');
                if (confirmDeleteButton) {
                    confirmDeleteButton.addEventListener('click', function () {
                        if (deleteId !== null) {
                            Livewire.emit('deleteSession', deleteId); // Livewireイベントをトリガー
                            closeModal();
                        }
                    });
                }
            });
        </script>
        <!-- livewireで決まり文句 </body>の直前である必要-->
        <script src="/vendor/livewire/livewire.js"></script>

    </body>
</html>
