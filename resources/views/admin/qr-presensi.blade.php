<!DOCTYPE html>
<html>

<head>
    <title>QR Presensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="flex items-center justify-center h-screen">
        <div class="bg-white p-10 rounded-xl shadow text-center">
            <h1 class="text-2xl font-bold mb-6">QR PRESENSI KARYAWAN</h1>

            <div id="qr-container">
                Loading QR...
            </div>
        </div>
    </div>

    <script>
        function loadQR() {
            fetch('/admin/generate-qr')
                .then(res => res.text())
                .then(data => {
                    document.getElementById('qr-container').innerHTML = data;
                });
        }

        loadQR();
        setInterval(loadQR, 10000);
    </script>

</body>

</html>