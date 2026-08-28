<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - FITC</title>
    <!-- SheetJS (xlsx) CDN untuk Import & Export Excel -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{ asset('storage/icon.png') }}" type="image/x-icon">
<link rel="apple-touch-icon" href="{{ asset('storage/icon.png') }}" sizes="180x180">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <!-- SweetAlert2 (Tambahan Baru) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 antialiased overflow-hidden">
    
    <div x-data="{ sidebarOpen: window.innerWidth >= 1024 }" 
         @resize.window="sidebarOpen = window.innerWidth >= 1024"
         class="flex h-screen w-full bg-gray-100">
        
        <!-- Panggil Sidebar -->
        @include('partials.sidebar')

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <!-- Panggil Topbar -->
            @include('partials.topbar')

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 md:p-6 lg:p-8">
                @yield('content')
            </main>
            
        </div>
    </div>

</body>
</html>