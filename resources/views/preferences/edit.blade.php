@extends('layouts.profile')

@section('title', __('messages.preferences.title'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="fas fa-heart mr-2"></i>{{ __('messages.preferences.title') }}
        </h1>
        <p class="text-gray-600 mb-8">{{ __('messages.preferences.description') }}</p>

        <form method="POST" action="{{ route('preferences.update') }}" class="space-y-8">
            @csrf

            <!-- Age Range -->
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('messages.preferences.age_range') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="min_age" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.preferences.min_age') }} *</label>
                        <input type="number" id="min_age" name="min_age" min="18" max="100" 
                               value="{{ old('min_age', $preferences?->min_age ?? 18) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                        @error('min_age')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_age" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.preferences.max_age') }} *</label>
                        <input type="number" id="max_age" name="max_age" min="18" max="100" 
                               value="{{ old('max_age', $preferences?->max_age ?? 100) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                        @error('max_age')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Geographic Matching -->
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>{{ __('messages.preferences.geographic_matching') }}
                </h2>
                
                <!-- Enable Geographic Matching Toggle -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">{{ __('messages.preferences.enable_geographic') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('messages.preferences.enable_geographic_desc') }}</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_geographic_matching" value="1" 
                               {{ old('enable_geographic_matching', $preferences?->enable_geographic_matching ?? true) ? 'checked' : '' }}
                               class="sr-only peer" id="enable_geographic_matching">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                    </label>
                </div>
                
                <!-- Distance Settings (only shown when geographic matching is enabled) -->
                <div id="distance_settings" class="space-y-4 {{ old('enable_geographic_matching', $preferences?->enable_geographic_matching ?? true) ? '' : 'hidden' }}">
                    <div>
                        <label for="max_distance" class="block text-sm font-medium text-gray-700 mb-2">
                            Distância máxima: <span id="distance_value">{{ old('max_distance', $preferences?->max_distance ?? 50) }}</span> km
                        </label>
                        <input type="range" id="max_distance" name="max_distance" min="1" max="1000" 
                               value="{{ old('max_distance', $preferences?->max_distance ?? 50) }}" 
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>1 km</span>
                            <span>100 km</span>
                            <span>500 km</span>
                            <span>1000 km</span>
                        </div>
                        @error('max_distance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                @if(!auth()->user()->hasGeolocation())
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                            <div>
                                <h3 class="text-sm font-medium text-yellow-800">Localização não configurada</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Para usar filtros de distância, configure sua localização primeiro.
                                    <a href="{{ route('location.index') }}" class="underline hover:text-yellow-800">Configurar localização</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Preferred Genders -->
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">Gêneros de Interesse</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_genders[]" value="male" 
                               {{ in_array('male', old('preferred_genders', $preferences?->preferred_genders ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Masculino</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_genders[]" value="female" 
                               {{ in_array('female', old('preferred_genders', $preferences?->preferred_genders ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Feminino</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_genders[]" value="other" 
                               {{ in_array('other', old('preferred_genders', $preferences?->preferred_genders ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Outro</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_genders[]" value="prefer_not_to_say" 
                               {{ in_array('prefer_not_to_say', old('preferred_genders', $preferences?->preferred_genders ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Prefiro não dizer</span>
                    </label>
                </div>
            </div>

            <!-- Relationship Goals -->
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">Objetivos de Relacionamento</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_relationship_goals[]" value="friendship" 
                               {{ in_array('friendship', old('preferred_relationship_goals', $preferences?->preferred_relationship_goals ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Amizade</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_relationship_goals[]" value="romance" 
                               {{ in_array('romance', old('preferred_relationship_goals', $preferences?->preferred_relationship_goals ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Romance</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_relationship_goals[]" value="casual" 
                               {{ in_array('casual', old('preferred_relationship_goals', $preferences?->preferred_relationship_goals ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Casual</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_relationship_goals[]" value="serious" 
                               {{ in_array('serious', old('preferred_relationship_goals', $preferences?->preferred_relationship_goals ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Relacionamento Sério</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_relationship_goals[]" value="marriage" 
                               {{ in_array('marriage', old('preferred_relationship_goals', $preferences?->preferred_relationship_goals ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Casamento</span>
                    </label>
                </div>
            </div>

            <!-- Education Levels -->
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">Níveis de Educação</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_education_levels[]" value="high_school" 
                               {{ in_array('high_school', old('preferred_education_levels', $preferences?->preferred_education_levels ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Ensino Médio</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_education_levels[]" value="bachelor" 
                               {{ in_array('bachelor', old('preferred_education_levels', $preferences?->preferred_education_levels ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Graduação</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_education_levels[]" value="master" 
                               {{ in_array('master', old('preferred_education_levels', $preferences?->preferred_education_levels ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Mestrado</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_education_levels[]" value="phd" 
                               {{ in_array('phd', old('preferred_education_levels', $preferences?->preferred_education_levels ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Doutorado</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="preferred_education_levels[]" value="other" 
                               {{ in_array('other', old('preferred_education_levels', $preferences?->preferred_education_levels ?? [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Outro</span>
                    </label>
                </div>
            </div>

            <!-- Lifestyle Preferences -->
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">Preferências de Estilo de Vida</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Aceita fumantes</h3>
                            <p class="text-sm text-gray-500">Incluir pessoas que fumam nos resultados</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="smoking_ok" value="1" 
                                   {{ old('smoking_ok', $preferences?->smoking_ok ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Aceita quem bebe</h3>
                            <p class="text-sm text-gray-500">Incluir pessoas que bebem nos resultados</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="drinking_ok" value="1" 
                                   {{ old('drinking_ok', $preferences?->drinking_ok ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Apenas online</h3>
                            <p class="text-sm text-gray-500">Mostrar apenas pessoas que estão online agora</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="online_only" value="1" 
                                   {{ old('online_only', $preferences?->online_only ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Apenas verificados</h3>
                            <p class="text-sm text-gray-500">Mostrar apenas perfis verificados</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="verified_only" value="1" 
                                   {{ old('verified_only', $preferences?->verified_only ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Somente perfis com fotos</h3>
                            <p class="text-sm text-gray-500">Mostrar apenas pessoas que têm fotos no perfil</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="photos_only" value="1" 
                                   {{ old('photos_only', $preferences?->photos_only ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Somente perfis completos</h3>
                            <p class="text-sm text-gray-500">Mostrar apenas pessoas com perfis 100% completos</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="complete_profiles_only" value="1" 
                                   {{ old('complete_profiles_only', $preferences?->complete_profiles_only ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit" class="bg-pink-600 text-white px-8 py-3 rounded-lg hover:bg-pink-700 transition duration-200 font-medium">
                    <i class="fas fa-save mr-2"></i>Salvar Preferências
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const distanceSlider = document.getElementById('max_distance');
    const distanceValue = document.getElementById('distance_value');
    const enableGeographicMatching = document.getElementById('enable_geographic_matching');
    const distanceSettings = document.getElementById('distance_settings');
    
    // Toggle distance settings visibility
    enableGeographicMatching.addEventListener('change', function() {
        if (this.checked) {
            distanceSettings.classList.remove('hidden');
            distanceSlider.required = true;
        } else {
            distanceSettings.classList.add('hidden');
            distanceSlider.required = false;
        }
    });
    
    // Update distance value display
    distanceSlider.addEventListener('input', function() {
        distanceValue.textContent = this.value;
    });
    
    // Initialize slider styles
    const style = document.createElement('style');
    style.textContent = `
        .slider {
            -webkit-appearance: none;
            appearance: none;
            background: #e5e7eb;
            outline: none;
            border-radius: 8px;
        }
        
        .slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #ec4899;
            cursor: pointer;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: #ec4899;
            cursor: pointer;
            border-radius: 50%;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    `;
    document.head.appendChild(style);
});
</script>
@endsection
