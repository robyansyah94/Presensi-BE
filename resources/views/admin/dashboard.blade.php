@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Page Title Start -->
<div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">Dashboard</h4>

    <div class="md:flex hidden items-center gap-3 text-sm font-semibold">
        <a href="#" class="text-sm font-medium text-default-700">Tailzon</a>

        <i class="i-tabler-chevron-right text-lg shrink-0 text-default-500 rtl:rotate-180"></i>

        <a href="#" class="text-sm font-medium text-default-700">Menu</a>

        <i class="i-tabler-chevron-right text-lg shrink-0 text-default-500 rtl:rotate-180"></i>

        <a href="#" class="text-sm font-medium text-default-700" aria-current="page">Dashboard</a>
    </div>
</div>
<!-- Page Title End -->

<div class="grid xl:grid-cols-4 md:grid-cols-2 gap-6 mb-6">
    <div
        class="card group overflow-hidden transition-all duration-500 hover:shadow-lg hover:-translate-y-0.5">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs tracking-wide font-semibold uppercase text-default-700 mb-3">KARYAWAN</p>
                    <h4 class="font-semibold text-2xl text-default-700">{{ $totalKaryawan }}</h4>
                </div>

                <div
                    class="rounded-full flex justify-center items-center size-14 bg-primary/10 text-primary">
                    <i
                        class="material-symbols-rounded text-2xl transition-all group-hover:fill-1">people</i>
                </div>
            </div>
        </div>
    </div>

    <div
        class="card group overflow-hidden transition-all duration-500 hover:shadow-lg hover:-translate-y-0.5">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs tracking-wide font-semibold uppercase text-default-700 mb-3">
                        Kehadiran</p>
                    <h4 class="font-semibold text-2xl text-default-700">5</h4>
                </div>

                <div
                    class="rounded-full flex justify-center items-center size-14 bg-secondary/10 text-secondary">
                    <i
                        class="material-symbols-rounded text-2xl transition-all group-hover:fill-1" style="color:green">login</i>
                </div>
            </div>
        </div>
        <!-- <div id="total-sale"></div> -->
    </div>

    <div
        class="card group overflow-hidden transition-all duration-500 hover:shadow-lg hover:-translate-y-0.5">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs tracking-wide font-semibold uppercase text-default-700 mb-3">
                        KETIDAKHADIRAN</p>
                    <h4 class="font-semibold text-2xl text-default-700">5</h4>
                </div>

                <div
                    class="rounded-full flex justify-center items-center size-14 bg-secondary/10 text-secondary">
                    <i class="material-symbols-rounded text-2xl transition-all group-hover:fill-1" style="color:red">logout</i>
                </div>
            </div>
        </div>
        <!-- <div id="total-sale"></div> -->
    </div>

    <div
        class="card group overflow-hidden transition-all duration-500 hover:shadow-lg hover:-translate-y-0.5">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs tracking-wide font-semibold uppercase text-default-700 mb-3">PENGAJUAN</p>
                    <h4 class="font-semibold text-2xl text-default-700">5</h4>
                </div>

                <div
                    class="rounded-full flex justify-center items-center size-14 bg-danger/10 text-danger">
                    <i
                        class="material-symbols-rounded text-2xl transition-all group-hover:fill-1">list_alt_add</i>
                </div>
            </div>
        </div>
        <!-- <div id="chart4"></div> -->
    </div>
    
    <div
        class="card group overflow-hidden transition-all duration-500 hover:shadow-lg hover:-translate-y-0.5">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs tracking-wide font-semibold uppercase text-default-700 mb-3">PENGAJUAN</p>
                    <h4 class="font-semibold text-2xl text-default-700">5</h4>
                </div>

                <div
                    class="rounded-full flex justify-center items-center size-14 bg-danger/10 text-danger">
                    <i
                        class="material-symbols-rounded text-2xl transition-all group-hover:fill-1">list_alt_add</i>
                </div>
            </div>
        </div>
        <!-- <div id="chart4"></div> -->
    </div>
</div>


@endsection

@push('scripts')
<script src="{{ asset('admin/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('admin/js/pages/dashboard.js') }}"></script>

@endpush