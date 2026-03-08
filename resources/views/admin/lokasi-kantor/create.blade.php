@extends('admin.layouts.app')

@section('title', 'Tambah Lokasi Kantor')

@section('content')

<!-- Page Title -->
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">TAMBAH LOKASI KANTOR</h4>

    <a href="{{ route('lokasi-kantor.index') }}"
        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
        &larr; Kembali
    </a>
</div>

<div class="card p-6">
    <form action="{{ route('lokasi-kantor.store') }}" method="POST">
        @csrf

        {{-- Nama Kantor --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nama Kantor <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama_kantor"
                value="{{ old('nama_kantor') }}"
                placeholder="Contoh: Kantor Pusat Jakarta"
                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition {{ $errors->has('nama_kantor') ? 'border-red-400' : 'border-gray-300' }}">
            @error('nama_kantor')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Peta --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Pilih Lokasi di Peta
                <span class="text-xs text-gray-400 font-normal ml-1">
                    — klik peta untuk set koordinat, atau seret marker
                </span>
            </label>
            <div id="map" class="w-full rounded-lg border border-gray-300" style="height: 350px;"></div>
        </div>

        {{-- Latitude & Longitude --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Latitude <span class="text-red-500">*</span>
                </label>
                <input type="number" name="latitude" id="input_latitude"
                    value="{{ old('latitude') }}"
                    step="any" placeholder="-6.2000000"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition {{ $errors->has('latitude') ? 'border-red-400' : 'border-gray-300' }}">
                @error('latitude')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Longitude <span class="text-red-500">*</span>
                </label>
                <input type="number" name="longitude" id="input_longitude"
                    value="{{ old('longitude') }}"
                    step="any" placeholder="106.8166667"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition {{ $errors->has('longitude') ? 'border-red-400' : 'border-gray-300' }}">
                @error('longitude')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Radius --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Radius (meter) <span class="text-red-500">*</span>
            </label>
            <input type="number" name="radius_meter" id="input_radius"
                value="{{ old('radius_meter', 100) }}"
                min="1" placeholder="100"
                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition {{ $errors->has('radius_meter') ? 'border-red-400' : 'border-gray-300' }}">
            <p class="text-xs text-gray-400 mt-1">Karyawan harus berada dalam radius ini untuk dapat melakukan presensi.</p>
            @error('radius_meter')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tombol --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Simpan Lokasi
            </button>
            <a href="{{ route('lokasi-kantor.index') }}"
                class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                Batal
            </a>
        </div>

    </form>
</div>

{{-- Data old() untuk JS diteruskan via data attribute --}}
<div id="map-data"
    data-lat="{{ old('latitude') }}"
    data-lng="{{ old('longitude') }}"
    data-radius="{{ old('radius_meter', 100) }}"
    style="display:none;">
</div>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    var map = L.map('map').setView([-6.200000, 106.816666], 13);
    var marker = null;
    var circle = null;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    function setMarker(lat, lng) {
        var latlng = L.latLng(lat, lng);
        var radius = parseInt(document.getElementById('input_radius').value) || 100;

        if (marker) {
            marker.setLatLng(latlng);
            circle.setLatLng(latlng).setRadius(radius);
        } else {
            marker = L.marker(latlng, {
                draggable: true
            }).addTo(map);
            circle = L.circle(latlng, {
                radius: radius,
                color: '#3b82f6',
                fillOpacity: 0.15
            }).addTo(map);

            marker.on('dragend', function() {
                var pos = marker.getLatLng();
                document.getElementById('input_latitude').value = pos.lat.toFixed(7);
                document.getElementById('input_longitude').value = pos.lng.toFixed(7);
                circle.setLatLng(pos);
            });
        }
        map.setView(latlng, 15);
    }

    // Klik peta
    map.on('click', function(e) {
        document.getElementById('input_latitude').value = e.latlng.lat.toFixed(7);
        document.getElementById('input_longitude').value = e.latlng.lng.toFixed(7);
        setMarker(e.latlng.lat, e.latlng.lng);
    });

    // Input manual lat/lng
    ['input_latitude', 'input_longitude'].forEach(function(id) {
        document.getElementById(id).addEventListener('input', function() {
            var lat = parseFloat(document.getElementById('input_latitude').value);
            var lng = parseFloat(document.getElementById('input_longitude').value);
            if (!isNaN(lat) && !isNaN(lng)) setMarker(lat, lng);
        });
    });

    // Perubahan radius
    document.getElementById('input_radius').addEventListener('input', function() {
        if (circle) circle.setRadius(parseInt(this.value) || 0);
    });

    // Jika ada old() value (setelah validasi gagal), tampilkan marker
    var mapData = document.getElementById('map-data');
    var oldLat = parseFloat(mapData.dataset.lat);
    var oldLng = parseFloat(mapData.dataset.lng);
    if (!isNaN(oldLat) && !isNaN(oldLng)) {
        setMarker(oldLat, oldLng);
    }
</script>
@endpush