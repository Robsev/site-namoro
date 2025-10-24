@extends('layouts.profile')

@section('title', 'Configuração de Localização')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Configuração de Localização</h1>
            <p class="mt-2 text-gray-600">Configure sua localização para encontrar pessoas próximas a você.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Current Location -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                    Localização Atual
                </h2>
                
                @if($user->hasGeolocation())
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-latitude text-gray-400 w-5"></i>
                            <span class="ml-2 text-sm text-gray-600">Latitude: {{ $user->latitude }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-longitude text-gray-400 w-5"></i>
                            <span class="ml-2 text-sm text-gray-600">Longitude: {{ $user->longitude }}</span>
                        </div>
                        @if($user->city)
                            <div class="flex items-center">
                                <i class="fas fa-city text-gray-400 w-5"></i>
                                <span class="ml-2 text-sm text-gray-600">{{ $user->formatted_location }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-map-marker-alt text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">Nenhuma localização configurada</p>
                    </div>
                @endif
            </div>

            <!-- Location Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-cog text-green-500 mr-2"></i>
                    Configurações
                </h2>

                <!-- Auto-detect Location -->
                <div class="mb-6">
                    <button id="detectLocationBtn" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center justify-center">
                        <i class="fas fa-crosshairs mr-2"></i>
                        Detectar Localização Atual
                    </button>
                    <p class="text-xs text-gray-500 mt-2">Permita o acesso à localização no seu navegador</p>
                </div>

                <!-- Manual Location Search -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Buscar por Cidade
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="locationSearch" 
                               placeholder="Digite o nome da cidade..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button id="searchLocationBtn" 
                                class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div id="searchResults" class="mt-2 space-y-1 hidden"></div>
                </div>

                <!-- Manual Coordinates -->
                <div class="space-y-4">
                    <h3 class="text-sm font-medium text-gray-700">Ou insira as coordenadas manualmente:</h3>
                    
                    <form id="manualLocationForm" action="{{ route('location.update') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Latitude</label>
                                <input type="number" 
                                       name="latitude" 
                                       step="any" 
                                       value="{{ $user->latitude }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Longitude</label>
                                <input type="number" 
                                       name="longitude" 
                                       step="any" 
                                       value="{{ $user->longitude }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Endereço</label>
                            <input type="text" 
                                   name="address" 
                                   value="{{ $user->address }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Cidade</label>
                                <input type="text" 
                                       name="city" 
                                       value="{{ $user->city }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                                <input type="text" 
                                       name="state" 
                                       value="{{ $user->state }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">País</label>
                                <input type="text" 
                                       name="country" 
                                       value="{{ $user->country }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">CEP</label>
                                <input type="text" 
                                       name="postal_code" 
                                       value="{{ $user->postal_code }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full mt-6 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Salvar Localização
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Privacy Notice -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">Privacidade da Localização</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Sua localização é usada apenas para encontrar pessoas próximas a você. 
                        Você pode ajustar a distância máxima nas suas preferências de matching.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const detectBtn = document.getElementById('detectLocationBtn');
    const searchInput = document.getElementById('locationSearch');
    const searchBtn = document.getElementById('searchLocationBtn');
    const searchResults = document.getElementById('searchResults');
    const manualForm = document.getElementById('manualLocationForm');

    // Auto-detect location
    detectBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Get location details
                    fetch('{{ route("location.current") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            latitude: lat,
                            longitude: lng
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update form fields
                            document.querySelector('input[name="latitude"]').value = lat;
                            document.querySelector('input[name="longitude"]').value = lng;
                            if (data.location.city) document.querySelector('input[name="city"]').value = data.location.city;
                            if (data.location.state) document.querySelector('input[name="state"]').value = data.location.state;
                            if (data.location.country) document.querySelector('input[name="country"]').value = data.location.country;
                            if (data.location.postal_code) document.querySelector('input[name="postal_code"]').value = data.location.postal_code;
                            
                            // Auto-submit form
                            manualForm.submit();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Erro ao obter detalhes da localização');
                    });
                },
                function(error) {
                    alert('Erro ao acessar localização: ' + error.message);
                }
            );
        } else {
            alert('Geolocalização não é suportada por este navegador');
        }
    });

    // Search locations
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 3) {
            searchResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('location.search') }}?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(location => {
                            const div = document.createElement('div');
                            div.className = 'p-2 hover:bg-gray-100 cursor-pointer rounded text-sm';
                            div.innerHTML = `
                                <div class="font-medium">${location.display_name}</div>
                                <div class="text-gray-500 text-xs">${location.latitude}, ${location.longitude}</div>
                            `;
                            div.addEventListener('click', () => {
                                selectLocation(location);
                            });
                            searchResults.appendChild(div);
                        });
                        searchResults.classList.remove('hidden');
                    } else {
                        searchResults.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }, 500);
    });

    function selectLocation(location) {
        document.querySelector('input[name="latitude"]').value = location.latitude;
        document.querySelector('input[name="longitude"]').value = location.longitude;
        if (location.city) document.querySelector('input[name="city"]').value = location.city;
        if (location.state) document.querySelector('input[name="state"]').value = location.state;
        if (location.country) document.querySelector('input[name="country"]').value = location.country;
        if (location.postal_code) document.querySelector('input[name="postal_code"]').value = location.postal_code;
        
        searchResults.classList.add('hidden');
        searchInput.value = '';
    }
});
</script>
@endsection
