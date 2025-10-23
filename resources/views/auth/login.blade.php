<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Amigos Para Sempre</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-pink-100 to-purple-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Amigos Para Sempre</h1>
            <p class="text-gray-600">Conecte-se para encontrar seus amigos</p>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-4">
            <!-- Google Login -->
            <a href="{{ route('auth.google') }}" 
               class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-700 hover:bg-gray-50 transition duration-200">
                <i class="fab fa-google text-red-500 mr-3"></i>
                <span class="font-medium">Continuar com Google</span>
            </a>

            <!-- Microsoft Login -->
            <a href="{{ route('auth.microsoft') }}" 
               class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-700 hover:bg-gray-50 transition duration-200">
                <i class="fab fa-microsoft text-blue-500 mr-3"></i>
                <span class="font-medium">Continuar com Microsoft</span>
            </a>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">ou</span>
                </div>
            </div>

            <!-- Email/Password Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                        <span class="ml-2 text-sm text-gray-600">Lembrar de mim</span>
                    </label>
                    <a href="#" class="text-sm text-pink-600 hover:text-pink-500">Esqueceu a senha?</a>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-lg font-medium hover:from-pink-600 hover:to-purple-700 transition duration-200">
                    Entrar
                </button>
            </form>

            <!-- Register Link -->
            <div class="text-center">
                <p class="text-gray-600">
                    Não tem uma conta? 
                    <a href="{{ route('register') }}" class="text-pink-600 hover:text-pink-500 font-medium">Cadastre-se</a>
                </p>
            </div>
        </div>

        <!-- Language Selector -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-center space-x-4">
                <a href="#" class="text-sm text-gray-500 hover:text-gray-700">🇧🇷 Português</a>
                <a href="#" class="text-sm text-gray-500 hover:text-gray-700">🇺🇸 English</a>
                <a href="#" class="text-sm text-gray-500 hover:text-gray-700">🇪🇸 Español</a>
            </div>
        </div>
    </div>
</body>
</html>
