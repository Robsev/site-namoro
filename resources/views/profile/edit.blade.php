@extends('layouts.profile')

@section('title', 'Editar Perfil')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow-sm border mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('basic')" id="tab-basic" class="tab-button active py-4 px-1 border-b-2 border-pink-500 font-medium text-sm text-pink-600">
                    <i class="fas fa-user mr-2"></i>Informações Básicas
                </button>
                <button onclick="showTab('details')" id="tab-details" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-info-circle mr-2"></i>Detalhes do Perfil
                </button>
                <button onclick="showTab('photos')" id="tab-photos" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-images mr-2"></i>Fotos
                </button>
                <button onclick="showTab('privacy')" id="tab-privacy" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-shield-alt mr-2"></i>Privacidade
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="space-y-6">
        <!-- Basic Information Tab -->
        <div id="content-basic" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Informações Básicas</h2>
                
                <form method="POST" action="{{ route('profile.update.basic') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Sobrenome *</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Data de Nascimento *</label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                            @error('birth_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gênero *</label>
                            <select id="gender" name="gender" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                                <option value="">Selecione...</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Masculino</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Feminino</option>
                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Outro</option>
                                <option value="prefer_not_to_say" {{ old('gender', $user->gender) == 'prefer_not_to_say' ? 'selected' : '' }}>Prefiro não dizer</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>


                    <div class="flex justify-end">
                        <button type="submit" class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition duration-200">
                            <i class="fas fa-save mr-2"></i>Salvar Informações Básicas
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Profile Details Tab -->
        <div id="content-details" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Detalhes do Perfil</h2>
                
                <form method="POST" action="{{ route('profile.update.details') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Biografia</label>
                        <textarea id="bio" name="bio" rows="4" 
                                  placeholder="Conte um pouco sobre você..." 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">{{ old('bio', $user->profile?->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="occupation" class="block text-sm font-medium text-gray-700 mb-2">Profissão</label>
                            <input type="text" id="occupation" name="occupation" value="{{ old('occupation', $user->profile?->occupation) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            @error('occupation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="education_level" class="block text-sm font-medium text-gray-700 mb-2">Nível de Educação</label>
                            <select id="education_level" name="education_level" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">Selecione...</option>
                                <option value="high_school" {{ old('education_level', $user->profile?->education_level) == 'high_school' ? 'selected' : '' }}>Ensino Médio</option>
                                <option value="bachelor" {{ old('education_level', $user->profile?->education_level) == 'bachelor' ? 'selected' : '' }}>Graduação</option>
                                <option value="master" {{ old('education_level', $user->profile?->education_level) == 'master' ? 'selected' : '' }}>Mestrado</option>
                                <option value="phd" {{ old('education_level', $user->profile?->education_level) == 'phd' ? 'selected' : '' }}>Doutorado</option>
                                <option value="other" {{ old('education_level', $user->profile?->education_level) == 'other' ? 'selected' : '' }}>Outro</option>
                            </select>
                            @error('education_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="relationship_goal" class="block text-sm font-medium text-gray-700 mb-2">Objetivo de Relacionamento</label>
                        <select id="relationship_goal" name="relationship_goal" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="">Selecione...</option>
                            <option value="friendship" {{ old('relationship_goal', $user->profile?->relationship_goal) == 'friendship' ? 'selected' : '' }}>Amizade</option>
                            <option value="romance" {{ old('relationship_goal', $user->profile?->relationship_goal) == 'romance' ? 'selected' : '' }}>Romance</option>
                            <option value="casual" {{ old('relationship_goal', $user->profile?->relationship_goal) == 'casual' ? 'selected' : '' }}>Casual</option>
                            <option value="serious" {{ old('relationship_goal', $user->profile?->relationship_goal) == 'serious' ? 'selected' : '' }}>Relacionamento Sério</option>
                            <option value="marriage" {{ old('relationship_goal', $user->profile?->relationship_goal) == 'marriage' ? 'selected' : '' }}>Casamento</option>
                        </select>
                        @error('relationship_goal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="smoking" class="block text-sm font-medium text-gray-700 mb-2">Fuma?</label>
                            <select id="smoking" name="smoking" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">Selecione...</option>
                                <option value="never" {{ old('smoking', $user->profile?->smoking) == 'never' ? 'selected' : '' }}>Nunca</option>
                                <option value="occasionally" {{ old('smoking', $user->profile?->smoking) == 'occasionally' ? 'selected' : '' }}>Ocasionalmente</option>
                                <option value="regularly" {{ old('smoking', $user->profile?->smoking) == 'regularly' ? 'selected' : '' }}>Regularmente</option>
                                <option value="prefer_not_to_say" {{ old('smoking', $user->profile?->smoking) == 'prefer_not_to_say' ? 'selected' : '' }}>Prefiro não dizer</option>
                            </select>
                            @error('smoking')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="drinking" class="block text-sm font-medium text-gray-700 mb-2">Bebe?</label>
                            <select id="drinking" name="drinking" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">Selecione...</option>
                                <option value="never" {{ old('drinking', $user->profile?->drinking) == 'never' ? 'selected' : '' }}>Nunca</option>
                                <option value="occasionally" {{ old('drinking', $user->profile?->drinking) == 'occasionally' ? 'selected' : '' }}>Ocasionalmente</option>
                                <option value="regularly" {{ old('drinking', $user->profile?->drinking) == 'regularly' ? 'selected' : '' }}>Regularmente</option>
                                <option value="prefer_not_to_say" {{ old('drinking', $user->profile?->drinking) == 'prefer_not_to_say' ? 'selected' : '' }}>Prefiro não dizer</option>
                            </select>
                            @error('drinking')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="looking_for" class="block text-sm font-medium text-gray-700 mb-2">O que você procura?</label>
                        <textarea id="looking_for" name="looking_for" rows="3" 
                                  placeholder="Descreva o que você está procurando em um relacionamento..." 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">{{ old('looking_for', $user->profile?->looking_for) }}</textarea>
                        @error('looking_for')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition duration-200">
                            <i class="fas fa-save mr-2"></i>Salvar Detalhes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Photos Tab -->
        <div id="content-photos" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Gerenciar Fotos</h2>
                
                <!-- Upload New Photo -->
                <div class="mb-6 p-4 border-2 border-dashed border-gray-300 rounded-lg">
                    <form method="POST" action="{{ route('photos.store') }}" enctype="multipart/form-data" class="text-center">
                        @csrf
                        <div class="space-y-4">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                            <div>
                                <label for="photo" class="cursor-pointer">
                                    <span class="mt-2 block text-sm font-medium text-gray-900">
                                        Clique para adicionar uma foto
                                    </span>
                                    <span class="mt-1 block text-sm text-gray-500">
                                        PNG, JPG, GIF até 5MB
                                    </span>
                                </label>
                                <input type="file" id="photo" name="photo" accept="image/*" class="hidden" onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Current Photos -->
                @if($user->photos->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($user->photos as $photo)
                    <div class="relative group">
                        <img src="{{ Storage::url($photo->photo_path) }}" 
                             alt="Foto do perfil" 
                             class="w-full h-48 object-cover rounded-lg">
                        
                        @if($photo->is_primary)
                        <div class="absolute top-2 left-2 bg-pink-600 text-white px-2 py-1 rounded text-xs font-medium">
                            Principal
                        </div>
                        @endif

                        @if(!$photo->is_approved)
                        <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded text-xs font-medium">
                            Pendente
                        </div>
                        @endif

                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <div class="flex space-x-2">
                                @if(!$photo->is_primary)
                                <form method="POST" action="{{ route('photos.primary', $photo) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-pink-600 text-white p-2 rounded-full hover:bg-pink-700 transition duration-200" title="Definir como principal">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                                @endif
                                
                                <form method="POST" action="{{ route('photos.destroy', $photo) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja deletar esta foto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition duration-200" title="Deletar foto">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-images text-4xl mb-4"></i>
                    <p>Nenhuma foto adicionada ainda.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Privacy Tab -->
        <div id="content-privacy" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Configurações de Privacidade</h2>
                
                <form method="POST" action="{{ route('profile.update.privacy') }}" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Mostrar distância</h3>
                                <p class="text-sm text-gray-500">Permitir que outros usuários vejam a distância entre vocês</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_distance" value="1" 
                                       {{ old('show_distance', $user->profile?->show_distance ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Mostrar idade</h3>
                                <p class="text-sm text-gray-500">Permitir que outros usuários vejam sua idade</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_age" value="1" 
                                       {{ old('show_age', $user->profile?->show_age ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Mostrar status online</h3>
                                <p class="text-sm text-gray-500">Permitir que outros usuários vejam quando você está online</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_online_status" value="1" 
                                       {{ old('show_online_status', $user->profile?->show_online_status ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition duration-200">
                            <i class="fas fa-save mr-2"></i>Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.add('hidden'));
    
    // Remove active class from all tab buttons
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(button => {
        button.classList.remove('active', 'border-pink-500', 'text-pink-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'border-pink-500', 'text-pink-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}
</script>
@endsection
