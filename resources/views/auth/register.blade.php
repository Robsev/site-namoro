<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.auth.register') }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-pink-100 to-purple-100 min-h-screen flex items-center justify-center py-8">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('messages.register.title') }}</h1>
            <p class="text-gray-600">{{ __('messages.auth.register_subtitle') }}</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            
            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           value="{{ old('first_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Sobrenome *</label>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           value="{{ old('last_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                </div>
            </div>

            <!-- Contact Information -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                       required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha *</label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha *</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                </div>
            </div>

            <!-- Personal Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento *</label>
                    <input type="date" 
                           id="birth_date" 
                           name="birth_date" 
                           value="{{ old('birth_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gênero *</label>
                    <select id="gender" 
                            name="gender"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                            required>
                        <option value="">Selecione...</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Feminino</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Outro</option>
                        <option value="prefer_not_to_say" {{ old('gender') == 'prefer_not_to_say' ? 'selected' : '' }}>Prefiro não dizer</option>
                    </select>
                </div>
            </div>

            <!-- Optional Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           value="{{ old('location') }}"
                           placeholder="Cidade, Estado"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="flex items-start">
                <input type="checkbox" 
                       id="terms" 
                       name="terms"
                       class="mt-1 rounded border-gray-300 text-pink-600 focus:ring-pink-500"
                       required>
                <label for="terms" class="ml-2 text-sm text-gray-600">
                    Eu concordo com os 
                    <a href="#" class="text-pink-600 hover:text-pink-500">Termos de Uso</a> 
                    e 
                    <a href="#" class="text-pink-600 hover:text-pink-500">Política de Privacidade</a>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-lg font-medium hover:from-pink-600 hover:to-purple-700 transition duration-200">
                Criar Conta
            </button>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-gray-600">
                    Já tem uma conta? 
                    <a href="{{ route('login') }}" class="text-pink-600 hover:text-pink-500 font-medium">Fazer login</a>
                </p>
            </div>
        </form>

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


