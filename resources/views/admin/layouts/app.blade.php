<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'presensi')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.ico') }}">
    <link href="{{ asset('admin/css/icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/app.min.css') }}" rel="stylesheet">
</head>

<body>
<div class="wrapper">

    {{-- SIDEBAR --}}
    @include('admin.layouts.sidebar')

    <div class="page-content">

        {{-- NAVBAR --}}
        @include('admin.layouts.navbar')

        {{-- ISI HALAMAN --}}
        <main class="container py-6">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        @include('admin.layouts.footer')

    </div>
</div>

{{-- JS --}}
<script src="{{ asset('admin/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('admin/libs/preline/preline.js') }}"></script>
<script src="{{ asset('admin/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/libs/iconify-icon/iconify-icon.min.js') }}"></script>
<script src="{{ asset('admin/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('admin/js/app.js') }}"></script>

@stack('scripts')
</body>
</html>
