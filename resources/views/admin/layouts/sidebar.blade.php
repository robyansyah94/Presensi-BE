<!-- Start Sidebar -->
<aside id="app-menu"
    class="w-sidenav min-w-sidenav bg-white shadow-sm overflow-y-auto hs-overlay fixed inset-y-0 start-0 z-60 hidden border-e border-default-200 -translate-x-full transform transition-all duration-200 hs-overlay-open:translate-x-0 lg:bottom-0 lg:end-auto lg:z-30 lg:block lg:translate-x-0 rtl:translate-x-full rtl:hs-overlay-open:translate-x-0 rtl:lg:translate-x-0 print:hidden [--body-scroll:true] [--overlay-backdrop:true] lg:[--overlay-backdrop:false]">

    <div class="flex flex-col h-full">
        <!-- Sidenav Logo -->
        <div class="sticky top-0 flex h-topbar items-center justify-start px-6">
            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('admin/images/logo-dark.png') }}" alt="logo" class="flex h-7">
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

                <!-- <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="app-calendar.html">
                        <i class="i-uil-calendar size-5"></i>
                        Calendar
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="app-gallery.html">
                        <i class="i-uil-image size-5"></i>
                        Images Gallery
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="app-plans.html">
                        <i class="i-uil-usd-circle size-5"></i>
                        Pricing Plans
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="app-contacts.html">
                        <i class="i-uil-user-circle size-5"></i>
                        Contacts
                    </a>
                </li>

                <li class="menu-item">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                        href="app-invoice.html">
                        <i class="i-uil-receipt-alt size-5"></i>
                        Invoice
                    </a>
                </li>

                <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">Pages</li>

                <li class="menu-item">
                    <a href="{{ url('#') }}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                        <i class="i-uil-clipboard size-5"></i>
                        <span class="menu-text"> Starter Pages </span>
                    </a>
                </li>

                <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">Elements</li>

                <li class="menu-item hs-accordion">
                    <a href="javascript:void(0)"
                        class="hs-accordion-toggle group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5 hs-accordion-active:bg-primary/5 hs-accordion-active:text-primary">
                        <i class="i-uil-apps size-5"></i>
                        <span class="menu-text"> Components </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                        <ul class="mt-1 space-y-1">
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-accordion.html">
                                    <i class="menu-dot"></i>
                                    Accordion
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-alerts.html">
                                    <i class="menu-dot"></i>
                                    Alert
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-avatars.html">
                                    <i class="menu-dot"></i>
                                    Avatars
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-buttons.html">
                                    <i class="menu-dot"></i>
                                    Buttons
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-badges.html">
                                    <i class="menu-dot"></i>
                                    Badges
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-breadcrumbs.html">
                                    <i class="menu-dot"></i>
                                    Breadcrumbs
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-cards.html">
                                    <i class="menu-dot"></i>
                                    Cards
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-collapse.html">
                                    <i class="menu-dot"></i>
                                    Collapse
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-dropdowns.html">
                                    <i class="menu-dot"></i>
                                    Dropdowns
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-progress.html">
                                    <i class="menu-dot"></i>
                                    Progress
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-spinners.html">
                                    <i class="menu-dot"></i>
                                    Spinners
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-skeleton.html">
                                    <i class="menu-dot"></i>
                                    Skeleton
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-ratio.html">
                                    <i class="menu-dot"></i>
                                    Ratio
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-modals.html">
                                    <i class="menu-dot"></i>
                                    Modals
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-offcanvas.html">
                                    <i class="menu-dot"></i>
                                    Offcanvas
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-popovers.html">
                                    <i class="menu-dot"></i>
                                    Popovers
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-tooltips.html">
                                    <i class="menu-dot"></i>
                                    Tooltips
                                </a>
                            </li>
                            <li class="menu-item">
                                <a class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5"
                                    href="ui-typography.html">
                                    <i class="menu-dot"></i>
                                    Typography
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-item hs-accordion">
                    <a href="javascript:void(0)"
                        class="hs-accordion-toggle group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5 hs-accordion-active:bg-primary/5 hs-accordion-active:text-primary">
                        <i class="i-uil-check-square size-5"></i>
                        <span class="menu-text"> Forms </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                        <ul class="mt-1 space-y-1">
                            <li class="menu-item">
                                <a href="{{ url('#') }}"
                                    class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                                    <i class="menu-dot"></i>
                                    <span class="menu-text">Inputs</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('#') }}"
                                    class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                                    <i class="menu-dot"></i>
                                    <span class="menu-text">Checkbox & Radio</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('#') }}"
                                    class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                                    <i class="menu-dot"></i>
                                    <span class="menu-text">Input Masks</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('#') }}"
                                    class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                                    <i class="menu-dot"></i>
                                    <span class="menu-text">File Upload</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('#') }}"
                                    class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                                    <i class="menu-dot"></i>
                                    <span class="menu-text">Editor</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('#') }}"
                                    class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                                    <i class="menu-dot"></i>
                                    <span class="menu-text">Validation</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('#') }}"
                                    class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                                    <i class="menu-dot"></i>
                                    <span class="menu-text">Form Layout</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-item">
                    <a href="{{ url('#') }}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                        <i class="i-uil-map-pin size-5"></i>
                        <span class="menu-text"> Maps </span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ url('#') }}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5 hs-accordion-active:bg-primary/5 hs-accordion-active:text-primary">
                        <i class="i-uil-table size-5"></i>
                        <span class="menu-text"> Tables </span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ url('#') }}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5 hs-accordion-active:bg-primary/5 hs-accordion-active:text-primary">
                        <i class="i-tabler-chart-donut-2 size-5"></i>
                        <span class="menu-text"> Chart </span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ url('#') }}"
                        class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                        <i class="i-uil-palette size-5"></i>
                        <span class="menu-text"> Icons </span>
                    </a>
                </li>

                <li class="menu-item hs-accordion">
                    <a href="javascript:void(0)"
                        class="hs-accordion-toggle group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5 hs-accordion-active:bg-primary/5 hs-accordion-active:text-primary">
                        <i class="i-uil-list-ui-alt size-5"></i>
                        <span class="menu-text"> Level </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div id="sidenavLevel"
                        class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                        <ul class="mt-1 space-y-1">
                            <li class="menu-item">
                                <a href="javascript: void(0)"
                                    class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                                    <i class="menu-dot"></i>
                                    <span class="menu-text">Item 1</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="javascript: void(0)"
                                    class="flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-600 transition-all hover:bg-primary/5">
                                    <i class="menu-dot"></i>
                                    <span class="menu-text">Item 2</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-item">
                    <a href="javascript:void(0)"
                        class="hs-accordion-toggle group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-600 transition-all hover:bg-primary/5 hs-accordion-active:bg-primary/5 hs-accordion-active:text-primary">
                        <i class="i-uil-star size-5"></i>
                        <span class="menu-text"> Badge Items </span>
                        <span
                            class="ms-auto inline-flex items-center gap-x-1.5 py-0.5 px-2 rounded-full font-bold text-xs bg-red-500 text-white">Hot</span>
                    </a>
                </li> -->
            </ul>
        </div>
    </div>
</aside>
<!-- End Sidebar -->