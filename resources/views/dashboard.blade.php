<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Amigos Para Sempre</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-800">Amigos Para Sempre</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Olá, {{ Auth::user()->first_name ?? Auth::user()->name }}!</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    @if(Auth::user()->profile_photo)
                        <img src="{{ Auth::user()->profile_photo }}" 
                             alt="Foto de perfil" 
                             class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    @else
                        <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-user text-gray-500 text-2xl"></i>
                        </div>
                    @endif
                    <h2 class="text-xl font-semibold text-gray-800">{{ Auth::user()->full_name }}</h2>
                    <p class="text-gray-600">{{ Auth::user()->email }}</p>
                    <div class="mt-4">
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                            {{ Auth::user()->subscription_type === 'premium' ? 'Premium' : 'Gratuito' }}
                        </span>
                        @if(Auth::user()->is_verified)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full ml-2">
                                <i class="fas fa-check"></i> Verificado
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ações Rápidas</h3>
                <div class="space-y-3">
        <a href="{{ route('matching.discover') }}" class="block w-full bg-pink-500 text-white py-2 px-4 rounded-lg text-center hover:bg-pink-600 transition duration-200">
            <i class="fas fa-search mr-2"></i>Descobrir Pessoas
        </a>
        <a href="{{ route('matching.matches') }}" class="block w-full bg-red-500 text-white py-2 px-4 rounded-lg text-center hover:bg-red-600 transition duration-200">
            <i class="fas fa-heart mr-2"></i>Meus Matches
        </a>
        <a href="{{ route('chat.conversations') }}" class="block w-full bg-green-500 text-white py-2 px-4 rounded-lg text-center hover:bg-green-600 transition duration-200">
            <i class="fas fa-comments mr-2"></i>Conversas
        </a>
        <a href="{{ route('notifications.index') }}" class="block w-full bg-yellow-500 text-white py-2 px-4 rounded-lg text-center hover:bg-yellow-600 transition duration-200 relative">
            <i class="fas fa-bell mr-2"></i>Notificações
            @if(Auth::user()->unread_notifications_count > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center">
                    {{ Auth::user()->unread_notifications_count }}
                </span>
            @endif
        </a>
        <a href="{{ route('profile.edit') }}" class="block w-full bg-purple-500 text-white py-2 px-4 rounded-lg text-center hover:bg-purple-600 transition duration-200">
            <i class="fas fa-edit mr-2"></i>Editar Perfil
        </a>
        <a href="{{ route('preferences.edit') }}" class="block w-full bg-indigo-500 text-white py-2 px-4 rounded-lg text-center hover:bg-indigo-600 transition duration-200">
            <i class="fas fa-cog mr-2"></i>Preferências
        </a>
        <a href="{{ route('profile.show') }}" class="block w-full bg-blue-500 text-white py-2 px-4 rounded-lg text-center hover:bg-blue-600 transition duration-200">
            <i class="fas fa-user mr-2"></i>Ver Perfil
        </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Estatísticas</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Matches</span>
                        <span class="font-semibold text-pink-600">0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Visualizações</span>
                        <span class="font-semibold text-purple-600">0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Likes</span>
                        <span class="font-semibold text-blue-600">0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Atividade Recente</h3>
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-heart text-4xl mb-4"></i>
                <p>Nenhuma atividade recente</p>
                <p class="text-sm">Complete seu perfil para começar a encontrar matches!</p>
            </div>
        </div>
    </div>
</body>
</html>


