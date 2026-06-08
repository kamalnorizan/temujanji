<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Temujanji') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="hold-transition layout-top-nav">
        <div class="wrapper">
            <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
                <div class="container">
                    <a href="{{ url('/') }}" class="navbar-brand">
                        <i class="fas fa-calendar-check me-2 text-primary"></i>
                        <span class="brand-text fw-semibold">{{ config('app.name', 'Temujanji') }}</span>
                    </a>

                    <ul class="navbar-nav ms-auto">
                        @if (Route::has('login'))
                            @auth
                                <li class="nav-item">
                                    <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a href="{{ route('login') }}" class="nav-link">Log in</a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a href="{{ route('register') }}" class="nav-link">Register</a>
                                    </li>
                                @endif
                            @endauth
                        @endif
                    </ul>
                </div>
            </nav>

            <div class="content-wrapper" style="margin-left: 0; min-height: calc(100vh - 57px);">
                <div class="content-header">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <h1 class="m-0">Sistem Temujanji Kaunseling</h1>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card card-primary card-outline">
                                    <div class="card-body">
                                        <h5 class="card-title">Pengurusan temujanji lebih teratur</h5>
                                        <p class="card-text mt-2">
                                            Pantau slot, status menunggu, dan kemaskini profil pengguna melalui antaramuka berasaskan AdminLTE.
                                        </p>
                                        @auth
                                            <a href="{{ route('dashboard') }}" class="btn btn-primary">Pergi ke Dashboard</a>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary me-2">Log in</a>
                                            @if (Route::has('register'))
                                                <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="small-box text-bg-info">
                                    <div class="inner">
                                        <h3>AdminLTE</h3>
                                        <p>Template aktif untuk seluruh UI aplikasi</p>
                                    </div>
                                    <div class="small-box-icon">
                                        <i class="fas fa-palette"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
