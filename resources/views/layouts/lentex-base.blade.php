<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- livewireで決まり文句 -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.lentex-navigation')

            <!-- Page Content -->
            <main class="container px-5 py-24 mx-auto">
                <div>
                    @if(!empty($alertComp))
                    <x-lentex-alert-completed :alert="$alertComp" />
                    @endif
                </div>

                @yield('contents')
            </main>
        </div>
        <!-- livewireで決まり文句 -->
        @livewireScripts
    </body>
</html>
