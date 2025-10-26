@extends('layouts.profile')

@section('title', 'Perfil Psicológico')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Perfil Psicológico</h1>
        <p class="mt-2 text-gray-600">Complete o questionário para descobrir seu perfil psicológico e melhorar o matching.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($profile && $profile->completed_at)
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <p class="text-blue-800 font-medium">Perfil já completado!</p>
                    <p class="text-blue-700 text-sm">Última atualização: {{ $profile->completed_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('psychological-profile.store') }}" class="space-y-8">
        @csrf
        
        <!-- Big Five - Openness -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-lightbulb text-purple-500 mr-2"></i>
                Abertura a Experiências
            </h2>
            <p class="text-gray-600 text-sm mb-6">Sua tendência a buscar novas experiências e ideias</p>
            
            <div class="space-y-4">
                <div class="question">
                    <p class="text-gray-700 mb-3">1. Eu gosto de experimentar coisas novas e diferentes</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[1]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[1]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[1]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[1]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[1]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">2. Eu tenho uma imaginação muito ativa</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[2]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[2]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[2]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[2]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[2]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">3. Eu gosto de arte e beleza</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[3]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[3]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[3]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[3]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[3]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Big Five - Conscientiousness -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-tasks text-green-500 mr-2"></i>
                Conscienciosidade
            </h2>
            <p class="text-gray-600 text-sm mb-6">Sua tendência a ser organizado e disciplinado</p>
            
            <div class="space-y-4">
                <div class="question">
                    <p class="text-gray-700 mb-3">4. Eu sempre termino o que começo</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[4]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[4]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[4]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[4]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[4]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">5. Eu sou muito organizado</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[5]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[5]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[5]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[5]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[5]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">6. Eu planejo com antecedência</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[6]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[6]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[6]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[6]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[6]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Big Five - Extraversion -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-users text-yellow-500 mr-2"></i>
                Extroversão
            </h2>
            <p class="text-gray-600 text-sm mb-6">Sua tendência a ser sociável e energético</p>
            
            <div class="space-y-4">
                <div class="question">
                    <p class="text-gray-700 mb-3">7. Eu sou o centro das atenções em grupos</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[7]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[7]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[7]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[7]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[7]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">8. Eu gosto de conhecer pessoas novas</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[8]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[8]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[8]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[8]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[8]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">9. Eu me sinto confortável em grandes grupos</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[9]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[9]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[9]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[9]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[9]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Big Five - Agreeableness -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-heart text-red-500 mr-2"></i>
                Amabilidade
            </h2>
            <p class="text-gray-600 text-sm mb-6">Sua tendência a ser cooperativo e confiante</p>
            
            <div class="space-y-4">
                <div class="question">
                    <p class="text-gray-700 mb-3">10. Eu confio nas pessoas</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[10]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[10]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[10]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[10]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[10]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">11. Eu gosto de ajudar os outros</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[11]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[11]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[11]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[11]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[11]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">12. Eu sou empático com os sentimentos dos outros</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[12]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[12]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[12]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[12]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[12]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Big Five - Neuroticism -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                Estabilidade Emocional
            </h2>
            <p class="text-gray-600 text-sm mb-6">Sua tendência a lidar com estresse e emoções negativas</p>
            
            <div class="space-y-4">
                <div class="question">
                    <p class="text-gray-700 mb-3">13. Eu me preocupo muito com as coisas</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[13]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[13]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[13]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[13]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[13]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">14. Eu fico facilmente estressado</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[14]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[14]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[14]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[14]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[14]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">15. Eu tenho mudanças de humor frequentes</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[15]" value="1" class="mr-1.5"> Discordo totalmente</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[15]" value="2" class="mr-1.5"> Discordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[15]" value="3" class="mr-1.5"> Neutro</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[15]" value="4" class="mr-1.5"> Concordo</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[15]" value="5" class="mr-1.5"> Concordo totalmente</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurações de Privacidade -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-lock text-gray-500 mr-2"></i>
                Privacidade
            </h2>
            
            <div class="flex items-center">
                <input type="checkbox" id="is_public" name="is_public" value="1" class="mr-3">
                <label for="is_public" class="text-gray-700">
                    Tornar meu perfil psicológico visível para outros usuários (recomendado para melhor matching)
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-pink-600 text-white px-8 py-3 rounded-lg hover:bg-pink-700 transition duration-200 font-medium">
                <i class="fas fa-brain mr-2"></i>Salvar Perfil Psicológico
            </button>
        </div>
    </form>
</div>

<style>
.question {
    border-bottom: 1px solid #f3f4f6;
    padding-bottom: 1rem;
}

.question:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

input[type="radio"] {
    accent-color: #ec4899;
}
</style>
@endsection
