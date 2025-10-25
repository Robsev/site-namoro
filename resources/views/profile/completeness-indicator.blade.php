@php
    $completeness = Auth::user()->profile_completeness;
    $level = Auth::user()->profile_completeness_level;
    $label = Auth::user()->profile_completeness_label;
    $missing = Auth::user()->missing_profile_fields;
@endphp

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-chart-pie text-blue-500 mr-2"></i>Completude do Perfil
        </h3>
        <span class="text-2xl font-bold text-{{ 
            $level === 'excellent' ? 'green' : 
            ($level === 'good' ? 'blue' : 
            ($level === 'fair' ? 'yellow' : 
            ($level === 'poor' ? 'orange' : 'red')))
        }}-500">
            {{ $completeness }}%
        </span>
    </div>

    <!-- Progress Bar -->
    <div class="mb-4">
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="h-3 rounded-full transition-all duration-500 ease-in-out bg-gradient-to-r from-red-500 via-yellow-500 to-green-500" 
                 style="width: {{ $completeness }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-1">
            <span>0%</span>
            <span class="font-medium">{{ $label }}</span>
            <span>100%</span>
        </div>
    </div>

    <!-- Missing Fields -->
    @if(count($missing) > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-yellow-800 mb-2">
                <i class="fas fa-exclamation-triangle mr-1"></i>Para completar seu perfil:
            </h4>
            <ul class="text-sm text-yellow-700 space-y-1">
                @foreach($missing as $field)
                    <li class="flex items-center">
                        <i class="fas fa-circle text-yellow-400 text-xs mr-2"></i>
                        {{ $field }}
                    </li>
                @endforeach
            </ul>
            <div class="mt-3">
                <a href="{{ route('profile.edit') }}" 
                   class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition duration-200">
                    <i class="fas fa-edit mr-1"></i>
                    Completar Perfil
                </a>
            </div>
        </div>
    @else
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <span class="text-green-800 font-medium">Perfil completo! Parabéns!</span>
            </div>
        </div>
    @endif

    <!-- Tips for better profile -->
    @if($completeness < 75)
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">
                <i class="fas fa-lightbulb mr-1"></i>Dicas para melhorar seu perfil:
            </h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Adicione uma biografia interessante que conte sua história</li>
                <li>• Selecione pelo menos 3 interesses que você gosta</li>
                <li>• Adicione 2-3 fotos de boa qualidade</li>
                <li>• Preencha o questionário psicológico para melhor matching</li>
            </ul>
        </div>
    @endif
</div>
