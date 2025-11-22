<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manutenção - {{ config('app.name', 'Sintonia de Amor') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 min-h-screen">
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
                        <div class="bg-yellow-100 rounded-full p-4">
                            <i class="fas fa-tools text-3xl text-yellow-600"></i>
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            Estamos em Manutenção
                        </h1>
                        <p class="text-gray-600">
                            Estamos trabalhando para melhorar sua experiência
                        </p>
                    </div>

                    <!-- Description -->
                    <div class="text-left bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            O que está acontecendo?
                        </h3>
                        <p class="text-sm text-gray-600 mb-3">
                            Estamos realizando melhorias no sistema para trazer uma experiência ainda melhor para você.
                        </p>
                        
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <i class="fas fa-clock text-green-500 mr-2"></i>
                            Quando voltaremos?
                        </h3>
                        <p class="text-sm text-gray-600">
                            Normalmente, nossa manutenção leva de 30 minutos a 2 horas. 
                            Agradecemos sua paciência!
                        </p>
                    </div>

                    <!-- Features Coming Soon -->
                    <div class="text-left">
                        <h3 class="font-semibold text-gray-900 mb-3 text-center">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                            Novidades em Breve
                        </h3>
                        <div class="grid grid-cols-1 gap-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span>Algoritmo de matching aprimorado</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span>Interface mais intuitiva</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span>Novos recursos de comunicação</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span>Melhor performance</span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="bg-pink-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <i class="fas fa-envelope text-pink-500 mr-2"></i>
                            Precisa de ajuda?
                        </h3>
                        <p class="text-sm text-gray-600">
                            Entre em contato conosco em caso de dúvidas ou emergências:
                        </p>
                        <div class="mt-2 space-y-1">
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

                    <!-- Refresh Button -->
                    <div class="pt-4">
                        <button onclick="window.location.reload()" 
                                class="w-full bg-pink-600 text-white py-3 px-6 rounded-lg hover:bg-pink-700 transition duration-200 font-medium">
                            <i class="fas fa-refresh mr-2"></i>
                            Tentar Novamente
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} Sintonia de Amor. Todos os direitos reservados.</p>
                <p class="mt-1">Conectando pessoas, criando amizades para sempre ❤️</p>
            </div>
        </div>
    </div>

    <!-- Auto-refresh script -->
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            window.location.reload();
        }, 30000);

        // Show countdown
        let countdown = 30;
        const button = document.querySelector('button');
        
        setInterval(function() {
            countdown--;
            if (countdown > 0) {
                button.innerHTML = `<i class="fas fa-refresh mr-2"></i>Tentar Novamente (${countdown}s)`;
            } else {
                button.innerHTML = `<i class="fas fa-refresh mr-2"></i>Tentar Novamente`;
                countdown = 30;
            }
        }, 1000);
    </script>
</body>
</html>
