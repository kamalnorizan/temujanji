@extends('layouts.app')

@section('header')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <h1 class="m-0">Dashboard</h1>
        <span class="badge text-bg-light border">Dikemaskini: {{ now()->format('d/m/Y H:i') }}</span>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>24</h3>
                    <p>Temujanji Hari Ini</p>
                </div>
                <div class="small-box-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <a href="#" class="small-box-footer text-decoration-none">
                    Lihat semua <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>8</h3>
                    <p>Sedang Menunggu</p>
                </div>
                <div class="small-box-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <a href="#" class="small-box-footer text-decoration-none">
                    Semak giliran <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h3>3</h3>
                    <p>Bilik Aktif</p>
                </div>
                <div class="small-box-icon">
                    <i class="fas fa-door-open"></i>
                </div>
                <a href="#" class="small-box-footer text-decoration-none">
                    Status bilik <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="small-box text-bg-danger">
                <div class="inner">
                    <h3>2</h3>
                    <p>Perlu Tindakan</p>
                </div>
                <div class="small-box-icon">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <a href="#" class="small-box-footer text-decoration-none">
                    Selesaikan isu <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Temujanji Terkini</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Masa</th>
                                    <th>Bilik</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#A-102</td>
                                    <td>Aina Rahim</td>
                                    <td>09:00</td>
                                    <td>Kaunseling 1</td>
                                    <td><span class="badge text-bg-success">Selesai</span></td>
                                </tr>
                                <tr>
                                    <td>#A-103</td>
                                    <td>Hakim Zain</td>
                                    <td>09:30</td>
                                    <td>Kaunseling 2</td>
                                    <td><span class="badge text-bg-warning">Menunggu</span></td>
                                </tr>
                                <tr>
                                    <td>#A-104</td>
                                    <td>Nurul Izzah</td>
                                    <td>10:00</td>
                                    <td>Kaunseling 3</td>
                                    <td><span class="badge text-bg-info">Sedang Berjalan</span></td>
                                </tr>
                                <tr>
                                    <td>#A-105</td>
                                    <td>Firdaus Kamil</td>
                                    <td>10:30</td>
                                    <td>Kaunseling 1</td>
                                    <td><span class="badge text-bg-danger">Perlu Semak</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="#" class="btn btn-sm btn-primary">Lihat Senarai Penuh</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Kapasiti Bilik</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Bilik 1</span>
                            <span>80%</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width: 80%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Bilik 2</span>
                            <span>60%</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" style="width: 60%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Bilik 3</span>
                            <span>95%</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-danger" style="width: 95%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Tindakan Pantas</h3>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Temujanji
                    </a>
                    <a href="#" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-users me-1"></i> Urus Pengguna
                    </a>
                    <a href="#" class="btn btn-outline-dark btn-sm">
                        <i class="fas fa-file-export me-1"></i> Eksport Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
