<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite('resources/css/app.css')
    </head>
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <div class="app-wrapper">
            @include('layouts.navigation')

            <main class="app-main">
                @hasSection('header')
                    <div class="app-content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    @yield('header')
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="app-content">
                    <div class="container-fluid py-2">
                        @yield('content')
                    </div>
                </div>
            </main>

            <footer class="app-footer">
                <strong>{{ config('app.name', 'Temujanji') }}</strong>
                <div class="float-right d-none d-sm-inline-block">
                    <b>Version</b> 1.0.0
                </div>
            </footer>
        </div>
        @vite('resources/js/app.js')
        @stack('scripts')
    </body>
</html>
