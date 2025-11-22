<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - {{ config('app.name', 'Sintonia de Amor') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Admin Navigation -->
        <nav class="bg-gray-800 shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('admin.photos.index') }}" class="text-2xl font-bold text-white">
                            <i class="fas fa-cog mr-2"></i>Admin Panel
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.photos.index') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-images mr-1"></i>Moderação
                        </a>
                        @php($pendingReportsCount = \App\Models\UserReport::where('status','pending')->count())
                        <a href="{{ route('admin.reports.index') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium relative">
                            <i class="fas fa-flag mr-1"></i>{{ __('messages.admin.reports') }}
                            @if($pendingReportsCount > 0)
                                <span class="absolute -top-1 -right-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">{{ $pendingReportsCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.photos.statistics') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-chart-bar mr-1"></i>Estatísticas
                        </a>
                        <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-home mr-1"></i>Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-sign-out-alt mr-1"></i>Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script>
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
