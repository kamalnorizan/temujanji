<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Dashboard</h1>
    </x-slot>

    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>24</h3>
                    <p>Temujanji Hari Ini</p>
                </div>
                <div class="small-box-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>8</h3>
                    <p>Sedang Menunggu</p>
                </div>
                <div class="small-box-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h3>3</h3>
                    <p>Bilik Aktif</p>
                </div>
                <div class="small-box-icon">
                    <i class="fas fa-door-open"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status Sistem</h3>
                </div>
                <div class="card-body">
                    <p class="mb-0">Anda berjaya log masuk. Tema aplikasi kini menggunakan AdminLTE secara keseluruhan untuk halaman berasaskan layout utama.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
