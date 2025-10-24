<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Erro Interno - {{ config('app.name', 'Amigos Para Sempre') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-red-50 via-pink-50 to-purple-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Logo -->
            <div class="flex justify-center">
                <div class="bg-white rounded-full p-4 shadow-lg">
                    <i class="fas fa-heart text-6xl text-pink-500"></i>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="space-y-6">
                    <!-- Icon -->
                    <div class="flex justify-center">
                        <div class="bg-red-100 rounded-full p-4">
                            <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            Ops! Algo deu errado
                        </h1>
                        <p class="text-gray-600">
                            Encontramos um problema interno no servidor
                        </p>
                    </div>

                    <!-- Error Code -->
                    <div class="bg-gray-100 rounded-lg p-4">
                        <p class="text-2xl font-bold text-gray-800">500</p>
                        <p class="text-sm text-gray-600">Erro Interno do Servidor</p>
                    </div>

                    <!-- Description -->
                    <div class="text-left bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            O que aconteceu?
                        </h3>
                        <p class="text-sm text-gray-600 mb-3">
                            Nosso servidor encontrou um erro inesperado e não conseguiu processar sua solicitação.
                        </p>
                        
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <i class="fas fa-wrench text-green-500 mr-2"></i>
                            O que fazer?
                        </h3>
                        <p class="text-sm text-gray-600">
                            Tente novamente em alguns minutos. Se o problema persistir, entre em contato conosco.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        <button onclick="window.history.back()" 
                                class="w-full bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition duration-200 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Voltar
                        </button>
                        
                        <button onclick="window.location.href='{{ url('/') }}'" 
                                class="w-full bg-pink-600 text-white py-3 px-6 rounded-lg hover:bg-pink-700 transition duration-200 font-medium">
                            <i class="fas fa-home mr-2"></i>
                            Ir para o Início
                        </button>
                    </div>

                    <!-- Contact Info -->
                    <div class="bg-pink-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <i class="fas fa-envelope text-pink-500 mr-2"></i>
                            Precisa de ajuda?
                        </h3>
                        <p class="text-sm text-gray-600 mb-2">
                            Se o problema persistir, entre em contato conosco:
                        </p>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-700">
                                <i class="fas fa-envelope mr-2"></i>
                                suporte@amigosparasempre.com
                            </p>
                            <p class="text-sm text-gray-700">
                                <i class="fas fa-phone mr-2"></i>
                                (11) 99999-9999
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} Amigos Para Sempre. Todos os direitos reservados.</p>
                <p class="mt-1">Conectando pessoas, criando amizades para sempre ❤️</p>
            </div>
        </div>
    </div>
</body>
</html>
