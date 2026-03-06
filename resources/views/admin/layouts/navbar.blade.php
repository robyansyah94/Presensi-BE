<!-- Topbar Start -->
<div id="topbar" class="app-header min-h-topbar flex items-center sticky top-0 z-50 bg-white shadow-sm">
    <div class="container flex items-center">
        <div class="w-full flex items-center justify-between gap-4">
            <div class="flex items-center gap-5">
                <!-- Sidenav Menu Toggle Button -->
                <button
                    class="flex items-center text-default-500 rounded-lg cursor-pointer p-2 bg-white border border-default-200 hover:bg-primary/15 hover:text-primary hover:border-primary/5 transition-all"
                    data-hs-overlay="#app-menu" aria-label="Toggle navigation">
                    <i class="i-lucide-align-left text-2xl"></i>
                </button>

                <!-- Topbar Brand Logo -->
                <a href="index.html" class="md:hidden flex">
                    <img src="{{ asset('admin/images/logo_RS.png') }}" class="h-10" alt="Small logo">
                </a>

                <!-- Topbar Search -->
                <div class="md:flex hidden items-center relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <i class="i-tabler-search text-base"></i>
                    </div>
                    <input type="search"
                        class="form-input px-10 rounded-lg  bg-default-500/10 border-transparent focus:border-transparent w-80"
                        placeholder="Search...">
                    <button type="button" class="absolute inset-y-0 end-0 flex items-center pe-3">
                        <i class="i-tabler-microphone text-base hover:text-black"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-5">
                <!-- Language Dropdown Button -->
                <div class="hs-dropdown relative inline-flex [--placement:bottom-right]">
                    <button type="button"
                        class="hs-dropdown-toggle inline-flex items-center p-2 rounded-lg bg-white border border-default-200 hover:bg-primary/15 hover:text-primary transition-all">
                        <i class="i-lucide-globe text-2xl"></i>
                    </button>

                    <div
                        class="hs-dropdown-menu duration mt-2 min-w-48 rounded-lg border border-default-200 bg-white p-2 opacity-0 shadow-md transition-[opacity,margin] hs-dropdown-open:opacity-100 hidden">
                        <a href="javascript:void(0);"
                            class="flex items-center gap-2.5 py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100">
                            <img src="{{ asset('admin/images/flags/germany.jpg') }}" alt="user-image" class="h-4">
                            <span class="align-middle">German</span>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);"
                            class="flex items-center gap-2.5 py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100">
                            <img src="{{ asset('admin/images/flags/italy.jpg') }}" alt="user-image" class="h-4">
                            <span class="align-middle">Italian</span>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);"
                            class="flex items-center gap-2.5 py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100">
                            <img src="{{ asset('admin/images/flags/spain.jpg') }}" alt="user-image" class="h-4">
                            <span class="align-middle">Spanish</span>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);"
                            class="flex items-center gap-2.5 py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100">
                            <img src="{{ asset('admin/images/flags/russia.jpg') }}" alt="user-image" class="h-4">
                            <span class="align-middle">Russian</span>
                        </a>
                    </div>
                </div>

                <!-- Notification Dropdown Button -->
                <div class="hs-dropdown relative inline-flex [--placement:bottom-right]">
                    <button type="button"
                        class="hs-dropdown-toggle inline-flex items-center p-2 rounded-lg bg-white border border-default-200 hover:bg-primary/15 hover:text-primary transition-all">
                        <i class="i-lucide-bell text-2xl"></i>
                    </button>

                    <!-- Dropdown menu -->
                    <div
                        class="hs-dropdown-menu duration mt-2 w-full max-w-sm rounded-lg border border-default-200 bg-white opacity-0 shadow-md transition-[opacity,margin] hs-dropdown-open:opacity-100 hidden">
                        <div
                            class="block px-4 py-2 font-medium text-center text-default-700 rounded-t-lg bg-default-50">
                            Notifications
                        </div>

                        <div class="divide-y divide-default-100">
                            <a href="#" class="flex px-4 py-3 hover:bg-default-100">
                                <div class="shrink-0">
                                    <img class="rounded-lg w-11 h-11" src="{{ asset('admin/images/users/avatar-6.jpg') }}"
                                        alt="Alex image">
                                    <div
                                        class="absolute flex items-center justify-center w-5 h-5 ms-6 -mt-5 bg-green-500 border border-white rounded-lg">
                                        <i class="i-tabler-alert-circle text-white w-4 h-4"></i>
                                    </div>
                                </div>
                                <div class="w-full ps-3">
                                    <div class="text-default-500 text-sm mb-1.5">
                                        New alert from <span class="font-semibold text-default-900">Alex
                                            Johnson</span>:
                                        "System needs attention, check logs."
                                    </div>
                                    <div class="text-xs text-primary">2 minutes ago</div>
                                </div>
                            </a>

                            <a href="#" class="flex px-4 py-3 hover:bg-default-100">
                                <div class="shrink-0">
                                    <img class="rounded-lg w-11 h-11" src="{{ asset('admin/images/users/avatar-7.jpg') }}"
                                        alt="Sarah image">
                                    <div
                                        class="absolute flex items-center justify-center w-5 h-5 ms-6 -mt-5 bg-primary-600 border border-white rounded-lg">
                                        <i class="i-tabler-file-text text-white w-4 h-4"></i>
                                    </div>
                                </div>
                                <div class="w-full ps-3">
                                    <div class="text-default-500 text-sm mb-1.5">
                                        <span class="font-semibold text-default-900">Sarah Lee</span> shared a
                                        document with you.
                                    </div>
                                    <div class="text-xs text-primary">5 minutes ago</div>
                                </div>
                            </a>

                            <a href="#" class="flex px-4 py-3 hover:bg-default-100">
                                <div class="shrink-0">
                                    <img class="rounded-lg w-11 h-11" src="{{ asset('admin/images/users/avatar-8.jpg') }}"
                                        alt="Michael image">
                                    <div
                                        class="absolute flex items-center justify-center w-5 h-5 ms-6 -mt-5 bg-purple-500 border border-white rounded-lg">
                                        <i class="i-tabler-message text-white w-4 h-4"></i>
                                    </div>
                                </div>
                                <div class="w-full ps-3">
                                    <div class="text-default-500 text-sm mb-1.5">
                                        <span class="font-semibold text-default-900">Michael Clark</span> replied
                                        to your comment.
                                    </div>
                                    <div class="text-xs text-primary">15 minutes ago</div>
                                </div>
                            </a>

                            <a href="#" class="flex px-4 py-3 hover:bg-default-100">
                                <div class="shrink-0">
                                    <img class="rounded-lg w-11 h-11" src="{{ asset('admin/images/users/avatar-9.jpg') }}"
                                        alt="Emma image">
                                    <div
                                        class="absolute flex items-center justify-center w-5 h-5 ms-6 -mt-5 bg-pink-500 border border-white rounded-lg">
                                        <i class="i-tabler-heart text-white w-4 h-4"></i>
                                    </div>
                                </div>
                                <div class="w-full ps-3">
                                    <div class="text-default-500 text-sm mb-1.5">
                                        <span class="font-semibold text-default-900">Emma Stone</span> reacted to
                                        your post.
                                    </div>
                                    <div class="text-xs text-primary">30 minutes ago</div>
                                </div>
                            </a>
                        </div>


                        <a href="#"
                            class="block py-2 text-sm font-medium text-center text-default-900 rounded-b-lg bg-default-50 hover:bg-default-100">
                            <div class="inline-flex items-center ">
                                <i class="i-tabler-eye size-4 text-default-500"></i>
                                View all
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Fullscreen Toggle Button -->
                <div class="md:flex hidden">
                    <button data-toggle="fullscreen" type="button"
                        class="p-2 rounded-lg bg-white border border-default-200 hover:bg-primary/15 hover:text-primary transition-all">
                        <span class="sr-only">Fullscreen Mode</span>
                        <span class="flex items-center justify-center size-6">
                            <i class="i-lucide-maximize text-2xl flex group-[-fullscreen]:hidden"></i>
                            <i class="i-lucide-minimize text-2xl hidden group-[-fullscreen]:flex"></i>
                        </span>
                    </button>
                </div>

                <!-- Profile Dropdown Button -->
                <div class="relative">
                    <div class="hs-dropdown relative inline-flex [--placement:bottom-right]">
                        <button type="button" class="hs-dropdown-toggle">
                            <img src="{{ asset('admin/images/users/avatar-8.jpg') }}" alt="user-image" class="rounded-lg h-10">
                        </button>
                        <div
                            class="hs-dropdown-menu duration mt-2 min-w-48 rounded-lg border border-default-200 bg-white p-2 opacity-0 shadow-md transition-[opacity,margin] hs-dropdown-open:opacity-100 hidden">
                            <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100"
                                href="#">
                                Profile
                            </a>
                            <!-- <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100"
                                            href="#">
                                            Feed
                                        </a>
                                        <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100"
                                            href="#">
                                            Analytics
                                        </a> -->
                            <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100"
                                href="#">
                                Settings
                            </a>
                            <!-- <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100"
                                            href="#">
                                            Support
                                        </a> -->

                            <hr class="my-2 -mx-2">

                            <form method="POST" action="/admin/logout">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full text-left py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Topbar End -->