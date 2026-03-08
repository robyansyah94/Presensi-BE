@extends('admin.layouts.app')

@section('title', 'Edit Lokasi Kantor')

@section('content')

<!-- Page Title -->
<div class="flex items-center justify-between flex-wrap gap-2 mb-5">
    <h4 class="text-default-900 text-lg font-semibold">EDIT LOKASI KANTOR</h4>

    <a href="{{ route('lokasi-kantor.index') }}"
        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
        &larr; Kembali
    </a>
</div>

<div class="card p-6">
    <form action="{{ route('lokasi-kantor.update', $lokasiKantor->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nama Kantor --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nama Kantor <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama_kantor"
                value="{{ old('nama_kantor', $lokasiKantor->nama_kantor) }}"
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
                    — klik peta untuk pindah lokasi, atau seret marker
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
                    value="{{ old('latitude', $lokasiKantor->latitude) }}"
                    step="any"
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
                    value="{{ old('longitude', $lokasiKantor->longitude) }}"
                    step="any"
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
                value="{{ old('radius_meter', $lokasiKantor->radius_meter) }}"
                min="1"
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
                Simpan Perubahan
            </button>
            <a href="{{ route('lokasi-kantor.index') }}"
                class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                Batal
            </a>
        </div>

    </form>
</div>

{{-- Data dari PHP diteruskan ke JS via data attribute, bukan inline Blade di dalam script --}}
<div id="map-data"
    data-lat="{{ old('latitude', $lokasiKantor->latitude) }}"
    data-lng="{{ old('longitude', $lokasiKantor->longitude) }}"
    data-radius="{{ old('radius_meter', $lokasiKantor->radius_meter) }}"
    style="display:none;">
</div>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    var mapData = document.getElementById('map-data');
    var initLat = parseFloat(mapData.dataset.lat);
    var initLng = parseFloat(mapData.dataset.lng);
    var initRadius = parseInt(mapData.dataset.radius);

    var map = L.map('map').setView([initLat, initLng], 15);
    var marker = L.marker([initLat, initLng], {
        draggable: true
    }).addTo(map);
    var circle = L.circle([initLat, initLng], {
        radius: initRadius,
        color: '#f59e0b',
        fillOpacity: 0.15
    }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    function updateInputs(lat, lng) {
        document.getElementById('input_latitude').value = lat.toFixed(7);
        document.getElementById('input_longitude').value = lng.toFixed(7);
    }

    // Klik peta
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        circle.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng);
    });

    // Drag marker
    marker.on('dragend', function() {
        var pos = marker.getLatLng();
        circle.setLatLng(pos);
        updateInputs(pos.lat, pos.lng);
    });

    // Input manual lat/lng
    ['input_latitude', 'input_longitude'].forEach(function(id) {
        document.getElementById(id).addEventListener('input', function() {
            var lat = parseFloat(document.getElementById('input_latitude').value);
            var lng = parseFloat(document.getElementById('input_longitude').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                var latlng = L.latLng(lat, lng);
                marker.setLatLng(latlng);
                circle.setLatLng(latlng);
                map.setView(latlng, 15);
            }
        });
    });

    // Perubahan radius
    document.getElementById('input_radius').addEventListener('input', function() {
        circle.setRadius(parseInt(this.value) || 0);
    });
</script>
@endpush