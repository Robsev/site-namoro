<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Amigos Para Sempre') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-heart text-4xl text-pink-500 mr-3"></i>
                    <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name', 'Amigos Para Sempre') }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Sair
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition duration-200">
                                <i class="fas fa-sign-in-alt mr-2"></i>Entrar
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition duration-200">
                                    <i class="fas fa-user-plus mr-2"></i>Cadastrar
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="text-center">
            <h1 class="text-5xl font-bold text-gray-900 mb-6">
                Conecte-se com <span class="text-pink-500">Amigos Para Sempre</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Encontre pessoas incríveis baseado em compatibilidade psicológica, interesses compartilhados e valores similares. 
                Construa amizades verdadeiras que duram para sempre.
            </p>
            
            @if (Route::has('login'))
                @guest
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="bg-pink-500 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-pink-600 transition duration-200 shadow-lg">
                            <i class="fas fa-heart mr-2"></i>Começar Agora
                        </a>
                        <a href="{{ route('login') }}" class="bg-white text-pink-500 px-8 py-4 rounded-lg text-lg font-semibold border-2 border-pink-500 hover:bg-pink-50 transition duration-200 shadow-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i>Já Tenho Conta
                        </a>
                    </div>
                @endguest
            @endif
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
                Por que escolher o Amigos Para Sempre?
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center p-6">
                    <div class="bg-pink-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-brain text-2xl text-pink-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Matching Inteligente</h3>
                    <p class="text-gray-600">
                        Algoritmo baseado em personalidade, interesses e valores para encontrar pessoas realmente compatíveis.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center p-6">
                    <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl text-purple-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Seguro e Verificado</h3>
                    <p class="text-gray-600">
                        Sistema de verificação e moderação para garantir um ambiente seguro e confiável.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center p-6">
                    <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-indigo-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Comunidade Ativa</h3>
                    <p class="text-gray-600">
                        Conecte-se com pessoas reais que compartilham seus interesses e valores.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="bg-gradient-to-br from-pink-50 to-purple-50 py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
                Como Funciona?
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="bg-pink-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        1
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Crie seu Perfil</h3>
                    <p class="text-gray-600 text-sm">
                        Conte-nos sobre você, seus interesses e o que procura em uma amizade.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="bg-purple-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        2
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Descubra Matches</h3>
                    <p class="text-gray-600 text-sm">
                        Nosso algoritmo encontra pessoas compatíveis com base em personalidade e interesses.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="bg-indigo-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        3
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Conecte-se</h3>
                    <p class="text-gray-600 text-sm">
                        Interaja com seus matches e descubra conexões genuínas.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="bg-green-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        4
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Construa Amizades</h3>
                    <p class="text-gray-600 text-sm">
                        Desenvolva relacionamentos duradouros e significativos.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-pink-500 py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">
                Pronto para encontrar seus amigos ideais?
            </h2>
            <p class="text-xl text-pink-100 mb-8">
                Junte-se a milhares de pessoas que já encontraram amizades verdadeiras.
            </p>
            
            @if (Route::has('login'))
                @guest
                    <a href="{{ route('register') }}" class="bg-white text-pink-500 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition duration-200 shadow-lg">
                        <i class="fas fa-heart mr-2"></i>Começar Gratuitamente
                    </a>
                @endguest
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-heart text-2xl text-pink-500 mr-2"></i>
                        <h3 class="text-xl font-bold">{{ config('app.name', 'Amigos Para Sempre') }}</h3>
                    </div>
                    <p class="text-gray-400">
                        Conectando pessoas, criando amizades para sempre.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Produto</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Como Funciona</a></li>
                        <li><a href="#" class="hover:text-white">Recursos</a></li>
                        <li><a href="#" class="hover:text-white">Preços</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Suporte</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Central de Ajuda</a></li>
                        <li><a href="#" class="hover:text-white">Contato</a></li>
                        <li><a href="#" class="hover:text-white">Comunidade</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Termos de Uso</a></li>
                        <li><a href="#" class="hover:text-white">Privacidade</a></li>
                        <li><a href="#" class="hover:text-white">Cookies</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Amigos Para Sempre') }}. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>