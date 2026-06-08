<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="hold-transition login-page" style="min-height: 100vh;">
        <div class="login-box" style="width: 420px; max-width: 94%;">
            <div class="login-logo">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <b>{{ config('app.name', 'Temujanji') }}</b>
                </a>
            </div>

            <div class="card card-outline card-primary shadow">
                <div class="card-body login-card-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </body>
</html>
