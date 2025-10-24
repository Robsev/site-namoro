@extends('layouts.profile')

@section('title', 'Meu Perfil')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
            <!-- Profile Photo -->
            <div class="relative">
                @if($user->profile_photo)
                    <img src="{{ Storage::url($user->profile_photo) }}" 
                         alt="Foto de perfil" 
                         class="w-32 h-32 rounded-full object-cover border-4 border-pink-200">
                @else
                    <div class="w-32 h-32 rounded-full bg-pink-100 flex items-center justify-center border-4 border-pink-200">
                        <i class="fas fa-user text-4xl text-pink-400"></i>
                    </div>
                @endif
                <div class="absolute -bottom-2 -right-2 bg-green-500 w-8 h-8 rounded-full border-4 border-white flex items-center justify-center">
                    <i class="fas fa-check text-white text-sm"></i>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ $user->full_name }}
                    @if($user->age)
                        <span class="text-xl text-gray-500">, {{ $user->age }} anos</span>
                    @endif
                </h1>
                
                @if($user->location)
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt mr-2"></i>{{ $user->location }}
                    </p>
                @endif

                @if($user->profile && $user->profile->bio)
                    <p class="text-gray-700 mb-4">{{ $user->profile->bio }}</p>
                @endif

                <div class="flex flex-wrap gap-2">
                    @if($user->profile && $user->profile->interests)
                        @foreach($user->profile->interests as $interest)
                            <span class="px-3 py-1 bg-pink-100 text-pink-800 text-sm rounded-full">
                                {{ $interest }}
                            </span>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-3">
                <a href="{{ route('profile.edit') }}" 
                   class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Editar Perfil
                </a>
                <a href="{{ route('preferences.edit') }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
                    <i class="fas fa-cog mr-2"></i>Preferências
                </a>
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-info-circle mr-2"></i>Informações Básicas
            </h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Nome:</span>
                    <span class="font-medium">{{ $user->full_name }}</span>
                </div>
                
                @if($user->age)
                <div class="flex justify-between">
                    <span class="text-gray-600">Idade:</span>
                    <span class="font-medium">{{ $user->age }} anos</span>
                </div>
                @endif

                @if($user->gender)
                <div class="flex justify-between">
                    <span class="text-gray-600">Gênero:</span>
                    <span class="font-medium capitalize">{{ $user->gender }}</span>
                </div>
                @endif

                @if($user->location)
                <div class="flex justify-between">
                    <span class="text-gray-600">Localização:</span>
                    <span class="font-medium">{{ $user->location }}</span>
                </div>
                @endif

                @if($user->phone)
                <div class="flex justify-between">
                    <span class="text-gray-600">Telefone:</span>
                    <span class="font-medium">{{ $user->phone }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Profile Details -->
        @if($user->profile)
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-user-circle mr-2"></i>Detalhes do Perfil
            </h2>
            
            <div class="space-y-3">
                @if($user->profile->occupation)
                <div class="flex justify-between">
                    <span class="text-gray-600">Profissão:</span>
                    <span class="font-medium">{{ $user->profile->occupation }}</span>
                </div>
                @endif

                @if($user->profile->education_level)
                <div class="flex justify-between">
                    <span class="text-gray-600">Educação:</span>
                    <span class="font-medium capitalize">{{ str_replace('_', ' ', $user->profile->education_level) }}</span>
                </div>
                @endif

                @if($user->profile->relationship_goal)
                <div class="flex justify-between">
                    <span class="text-gray-600">Objetivo:</span>
                    <span class="font-medium capitalize">{{ str_replace('_', ' ', $user->profile->relationship_goal) }}</span>
                </div>
                @endif

                @if($user->profile->smoking)
                <div class="flex justify-between">
                    <span class="text-gray-600">Fuma:</span>
                    <span class="font-medium capitalize">{{ str_replace('_', ' ', $user->profile->smoking) }}</span>
                </div>
                @endif

                @if($user->profile->drinking)
                <div class="flex justify-between">
                    <span class="text-gray-600">Bebe:</span>
                    <span class="font-medium capitalize">{{ str_replace('_', ' ', $user->profile->drinking) }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Photos Section -->
    @if($user->photos->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border p-6 mt-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
            <i class="fas fa-images mr-2"></i>Minhas Fotos
        </h2>
        
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
                            <button type="submit" class="bg-pink-600 text-white p-2 rounded-full hover:bg-pink-700 transition duration-200">
                                <i class="fas fa-star"></i>
                            </button>
                        </form>
                        @endif
                        
                        <form method="POST" action="{{ route('photos.destroy', $photo) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja deletar esta foto?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition duration-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
