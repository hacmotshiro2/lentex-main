<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{$title ?? '今ついたよでたよ'}}</title>

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
        <!-- livewireで決まり文句 </body>の直前である必要-->
        @livewireScripts
        
    </body>
</html>
