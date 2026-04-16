<!-- Start Sidebar -->
<aside id="app-menu"
    class="w-sidenav min-w-sidenav bg-white shadow-sm overflow-y-auto hs-overlay fixed inset-y-0 start-0 z-60 hidden border-e border-default-200 -translate-x-full transform transition-all duration-200 hs-overlay-open:translate-x-0 lg:bottom-0 lg:end-auto lg:z-30 lg:block lg:translate-x-0 rtl:translate-x-full rtl:hs-overlay-open:translate-x-0 rtl:lg:translate-x-0 print:hidden [--body-scroll:true] [--overlay-backdrop:true] lg:[--overlay-backdrop:false]">

    <div class="flex flex-col h-full">
        <!-- Sidenav Logo -->
        <div class="sticky top-0 flex h-topbar items-center justify-start px-6">
            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('admin/images/Presensi.png') }}" alt="logo" class="flex">
            </a>
        </div>

        <div class="p-4 h-[calc(100%-theme('spacing.topbar'))] grow" data-simplebar>
            <!-- Menu -->
            <ul class="admin-menu hs-accordion-group flex w-full flex-col gap-1">
                <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">PLATFORM</li>

                <li class="menu-item hs-accordion">
                    <a href="{{ route('admin.dashboard') }}"
                        class="hs-accordion-toggle group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5 hs-accordion-active:bg-primary/5 hs-accordion-active:text-primary">
                        <i class="i-uil-home-alt size-5"></i>
                        <span class="menu-text"> Dashboard </span>
                        <!-- <span class="menu-arrow"></span> -->
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ url('admin/qr-presensi') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            qr_code_2
                        </i>
                        <span class="menu-text"> QR Presensi </span>
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('jadwal-shift.preview') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            calendar_clock
                        </i>
                        Schedule
                    </a>
                </li>

                <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">ATTENDANCE</li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('pengajuan.index') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            demography
                        </i>
                        Pengajuan Izin
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('attendance.history') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            history
                        </i>
                        History
                    </a>
                </li>

                <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">MANAGEMENT</li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('users.index') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            groups
                        </i>
                        Users
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('jabatan.index') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            account_tree
                        </i>
                        Jabatan
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('shift.index') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            schedule
                        </i>
                        Shift
                    </a>
                </li>

                <!-- assesmen -->
                <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">PENILAIAN</li>
                <li>
                    <a href="{{ route('admin.assessment.index') }}"
                        class="flex items-center gap-x-2 rounded-md px-3 py-2 text-sm font-medium transition-all
                                        {{ request()->routeIs('admin.assessment.index') || request()->routeIs('admin.assessment.create') || request()->routeIs('admin.assessment.edit') || request()->routeIs('admin.assessment.report')
                                            ? 'text-primary bg-primary/5'
                                            : 'text-default-600 hover:bg-primary/5' }}">
                        <i class="material-symbols-rounded" style="font-size: 18px;">grading</i>
                        Input Penilaian
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.assessment.categories.index') }}"
                        class="flex items-center gap-x-2 rounded-md px-3 py-2 text-sm font-medium transition-all
                                        {{ request()->routeIs('admin.assessment.categories.*')
                                            ? 'text-primary bg-primary/5'
                                            : 'text-default-600 hover:bg-primary/5' }}">
                        <i class="material-symbols-rounded" style="font-size: 18px;">category</i>
                        Kategori Penilaian
                    </a>
                </li>

                <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">Point</li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('admin.integrity.analytics.index') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            social_leaderboard
                        </i>
                        Leaderboard
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('admin.integrity.marketplace.index') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            local_mall
                        </i>
                        Marketplace
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('admin.integrity.rules.index') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            rule
                        </i>
                        Rules
                    </a>
                </li>
                
                <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">Settings</li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('jadwal-shift.index') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            calendar_add_on
                        </i>
                        Atur Schedule
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="{{ route('lokasi-kantor.index') }}">
                        <i class="material-symbols-rounded" style="font-size: 21px;">
                            location_on
                        </i>
                        Lokasi Kantor
                    </a>
                </li>
        </div>
</aside>
<!-- End Sidebar -->