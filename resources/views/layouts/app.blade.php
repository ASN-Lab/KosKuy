<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KosKuy - Temukan Kos & Barang Bekas Berkualitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">KosKuy</a>
                <div class="flex space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600">Beranda</a>
                    <a href="/kos" class="text-gray-700 hover:text-indigo-600">Kos</a>
                    <a href="/thrift" class="text-gray-700 hover:text-indigo-600">Thrift</a>
                    <a href="/admin" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Admin</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8 text-center">
            <p>&copy; 2026 KosKuy. Platform Kos & Barang Bekas Mahasiswa.</p>
        </div>
    </footer>
</body>
</html>
