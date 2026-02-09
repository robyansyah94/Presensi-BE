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
                    <p class="text-xs tracking-wide font-semibold uppercase text-default-700 mb-3">Total Pegawai</p>
                    <h4 class="font-semibold text-2xl text-default-700">120</h4>
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
                        Kehadiran</p>
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
                    <p class="text-xs tracking-wide font-semibold uppercase text-default-700 mb-3">Daily
                        Visit</p>
                    <h4 class="font-semibold text-2xl text-default-700">1,12,584</h4>
                </div>

                <div
                    class="rounded-full flex justify-center items-center size-14 bg-danger/10 text-danger">
                    <i
                        class="material-symbols-rounded text-2xl transition-all group-hover:fill-1">account_balance</i>
                </div>
            </div>
        </div>
        <!-- <div id="chart4"></div> -->
    </div>
</div>

<div class="grid xl:grid-cols-3 md:grid-cols-2 gap-6 mb-6">
    <div class="xl:col-span-2">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Total Revenue</h4>
                <div id="line-chart" class="morris-chart"></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Activity By Teams</h4>
            <div id="radial-chart" class="morris-chart"></div>
        </div> <!-- end card-body-->
    </div> <!-- end card-->
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Recent Customers</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="table-auto w-full border-collapse">
            <thead class="bg-default-100 border-b border-default-200">
                <tr>
                    <th class="px-4 py-2 text-left">Customer</th>
                    <th class="px-4 py-2 text-left">Phone</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Location</th>
                    <th class="px-4 py-2 text-left">Create Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-default-200">
                <tr class="hover:bg-default-50">
                    <td class="px-4 py-2 flex items-center gap-2">
                        <img src="{{ asset('admin/images/users/avatar-4.jpg') }}" alt="table-user"
                            class="size-9 rounded-full">
                        <a href="javascript:void(0);" class="font-semibold text-default-800">Paul
                            J. Friend</a>
                    </td>
                    <td class="px-4 py-2">937-330-1634</td>
                    <td class="px-4 py-2">pauljfrnd@jourrapide.com</td>
                    <td class="px-4 py-2">New York</td>
                    <td class="px-4 py-2">07/07/2024</td>
                </tr>
                <tr class="hover:bg-default-50">
                    <td class="px-4 py-2 flex items-center gap-2">
                        <img src="{{ asset('admin/images/users/avatar-3.jpg') }}" alt="table-user"
                            class="size-9 rounded-full">
                        <a href="javascript:void(0);" class="font-semibold text-default-800">Bryan
                            J. Luellen</a>
                    </td>
                    <td class="px-4 py-2">215-302-3376</td>
                    <td class="px-4 py-2">bryuellen@dayrep.com</td>
                    <td class="px-4 py-2">New York</td>
                    <td class="px-4 py-2">09/12/2024</td>
                </tr>
                <tr class="hover:bg-default-50">
                    <td class="px-4 py-2 flex items-center gap-2">
                        <img src="{{ asset('admin/images/users/avatar-8.jpg') }}" alt="table-user"
                            class="size-9 rounded-full">
                        <a href="javascript:void(0);" class="font-semibold text-default-800">Kathryn
                            S. Collier</a>
                    </td>
                    <td class="px-4 py-2">828-216-2190</td>
                    <td class="px-4 py-2">collier@jourrapide.com</td>
                    <td class="px-4 py-2">Canada</td>
                    <td class="px-4 py-2">06/30/2024</td>
                </tr>
                <tr class="hover:bg-default-50">
                    <td class="px-4 py-2 flex items-center gap-2">
                        <img src="{{ asset('admin/images/users/avatar-1.jpg') }}" alt="table-user"
                            class="size-9 rounded-full">
                        <a href="javascript:void(0);" class="font-semibold text-default-800">Timothy
                            Kauper</a>
                    </td>
                    <td class="px-4 py-2">(216) 75 612 706</td>
                    <td class="px-4 py-2">thykauper@rhyta.com</td>
                    <td class="px-4 py-2">Denmark</td>
                    <td class="px-4 py-2">09/08/2024</td>
                </tr>
                <tr class="hover:bg-default-50">
                    <td class="px-4 py-2 flex items-center gap-2">
                        <img src="{{ asset('admin/images/users/avatar-5.jpg') }}" alt="table-user"
                            class="size-9 rounded-full">
                        <a href="javascript:void(0);" class="font-semibold text-default-800">Zara
                            Raws</a>
                    </td>
                    <td class="px-4 py-2">(02) 75 150 655</td>
                    <td class="px-4 py-2">austin@dayrep.com</td>
                    <td class="px-4 py-2">Germany</td>
                    <td class="px-4 py-2">07/15/2024</td>
                </tr>
                <tr class="hover:bg-default-50">
                    <td class="px-4 py-2 flex items-center gap-2">
                        <img src="{{ asset('admin/images/users/avatar-6.jpg') }}" alt="table-user"
                            class="size-9 rounded-full">
                        <a href="javascript:void(0);" class="font-semibold text-default-800">Mike
                            John</a>
                    </td>
                    <td class="px-4 py-2">798-4651-455</td>
                    <td class="px-4 py-2">mikejohn@jourrapide.com</td>
                    <td class="px-4 py-2">New York</td>
                    <td class="px-4 py-2">08/07/2024</td>
                </tr>
            </tbody>
        </table>
    </div>
</div> <!-- end card-->

@endsection

@push('scripts')
<script src="{{ asset('admin/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('admin/js/pages/dashboard.js') }}"></script>
@endpush