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
            <!-- Map -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-map text-blue-500 mr-2"></i>
                    Mapa da Localização
                </h2>
                <div id="map" class="w-full h-96 rounded-lg border border-gray-200"></div>
                <div class="mt-4 text-sm text-gray-600">
                    <p><strong>Endereço:</strong> <span id="map-address">
                        @if($user->hasGeolocation())
                            {{ $user->formatted_location ?: 'Endereço não disponível' }}
                        @else
                            -
                        @endif
                    </span></p>
                </div>
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

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Bairro</label>
                                <input type="text" 
                                       name="neighborhood" 
                                       value="{{ $user->neighborhood }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Distrito</label>
                                <input type="text" 
                                       name="district" 
                                       value="{{ $user->district }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Rua</label>
                                <input type="text" 
                                       name="road" 
                                       value="{{ $user->road }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Número</label>
                                <input type="text" 
                                       name="house_number" 
                                       value="{{ $user->house_number }}"
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
// Variáveis globais para o mapa
let map;
let marker;
let currentLat = {{ $user->latitude ?? 'null' }};
let currentLng = {{ $user->longitude ?? 'null' }};

// Função global para inicializar Google Maps (chamada pela API)
function initGoogleMaps() {
    initMap();
}

// Detectar qual API de mapa está disponível
function detectMapAPI() {
    if (typeof google !== 'undefined' && google.maps) {
        return 'google';
    } else if (typeof L !== 'undefined') {
        return 'leaflet';
    }
    return null;
}

document.addEventListener('DOMContentLoaded', function() {
    const detectBtn = document.getElementById('detectLocationBtn');
    const searchInput = document.getElementById('locationSearch');
    const searchBtn = document.getElementById('searchLocationBtn');
    const searchResults = document.getElementById('searchResults');
    const manualForm = document.getElementById('manualLocationForm');

    // Inicializar mapa quando DOM estiver pronto
    const mapAPI = detectMapAPI();
    if (mapAPI) {
        initMap();
    } else {
        // Aguardar carregamento das APIs
        setTimeout(() => {
            const mapAPI = detectMapAPI();
            if (mapAPI) {
                initMap();
            } else {
                console.warn('Nenhuma API de mapa disponível');
            }
        }, 1000);
    }

    // Auto-detect location
    detectBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            // Mostrar loading
            detectBtn.disabled = true;
            detectBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Detectando...';
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    console.log('Localização detectada:', lat, lng, 'Precisão:', position.coords.accuracy + 'm');
                    
                    // Atualizar mapa IMEDIATAMENTE
                    updateMapWithLocation(lat, lng, 'Localização detectada');
                    
                    // Update form fields
                    document.querySelector('input[name="latitude"]').value = lat;
                    document.querySelector('input[name="longitude"]').value = lng;
                    
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
                            // Update form fields with detailed info
                            if (data.location.city) document.querySelector('input[name="city"]').value = data.location.city;
                            if (data.location.state) document.querySelector('input[name="state"]').value = data.location.state;
                            if (data.location.country) document.querySelector('input[name="country"]').value = data.location.country;
                            if (data.location.postal_code) document.querySelector('input[name="postal_code"]').value = data.location.postal_code;
                            if (data.location.neighborhood) document.querySelector('input[name="neighborhood"]').value = data.location.neighborhood;
                            if (data.location.district) document.querySelector('input[name="district"]').value = data.location.district;
                            if (data.location.road) document.querySelector('input[name="road"]').value = data.location.road;
                            if (data.location.house_number) document.querySelector('input[name="house_number"]').value = data.location.house_number;
                            
                            // Atualizar mapa com endereço completo
                            updateMapWithLocation(lat, lng, data.location.address || 'Localização detectada');
                            
                            // Auto-submit form
                            manualForm.submit();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Erro ao obter detalhes da localização, mas coordenadas foram detectadas');
                        // Mesmo com erro, submeter com as coordenadas
                        manualForm.submit();
                    })
                    .finally(() => {
                        // Restaurar botão
                        detectBtn.disabled = false;
                        detectBtn.innerHTML = '<i class="fas fa-crosshairs mr-2"></i>Detectar Localização Atual';
                    });
                },
                function(error) {
                    console.error('Geolocation error:', error);
                    let errorMessage = 'Erro ao acessar localização: ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage += 'Permissão negada. Ative a localização no navegador.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage += 'Localização indisponível.';
                            break;
                        case error.TIMEOUT:
                            errorMessage += 'Tempo esgotado. Tente novamente.';
                            break;
                        default:
                            errorMessage += error.message;
                            break;
                    }
                    alert(errorMessage);
                    
                    // Restaurar botão
                    detectBtn.disabled = false;
                    detectBtn.innerHTML = '<i class="fas fa-crosshairs mr-2"></i>Detectar Localização Atual';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 300000 // 5 minutos
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

// Função para inicializar o mapa
function initMap() {
    const mapAPI = detectMapAPI();
    if (!mapAPI) {
        console.error('Nenhuma API de mapa disponível');
        return;
    }
    
    // Coordenadas padrão (São Paulo) se não houver localização
    const defaultLat = currentLat || -23.5505;
    const defaultLng = currentLng || -46.6333;
    
    if (mapAPI === 'google') {
        // Inicializar Google Maps
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: defaultLat, lng: defaultLng },
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'on' }]
                }
            ]
        });
    } else if (mapAPI === 'leaflet') {
        // Inicializar OpenStreetMap com Leaflet
        map = L.map('map').setView([defaultLat, defaultLng], 15);
        
        // Adicionar tiles do OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
    }
    
    // Adicionar marcador se houver localização
    if (currentLat && currentLng) {
        addMarker(currentLat, currentLng, 'Sua localização atual');
        updateMapInfo(currentLat, currentLng, '{{ $user->formatted_location ?? "Localização atual" }}');
    }
}

// Função para adicionar/atualizar marcador
function addMarker(lat, lng, title = 'Localização') {
    const mapAPI = detectMapAPI();
    if (!mapAPI) return;
    
    // Remover marcador anterior se existir
    if (marker) {
        if (mapAPI === 'google') {
            marker.setMap(null);
        } else if (mapAPI === 'leaflet') {
            map.removeLayer(marker);
        }
    }
    
    if (mapAPI === 'google') {
        // Adicionar marcador do Google Maps
        marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            title: title,
            animation: google.maps.Animation.DROP,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(32, 32)
            }
        });
        
        // Adicionar info window
        const infoWindow = new google.maps.InfoWindow({
            content: `<div class="p-2"><strong>${title}</strong><br>${lat.toFixed(6)}, ${lng.toFixed(6)}</div>`
        });
        
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
        
        // Centralizar mapa na nova localização
        map.setCenter({ lat: lat, lng: lng });
        map.setZoom(15);
        
    } else if (mapAPI === 'leaflet') {
        // Adicionar marcador do Leaflet
        marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(`<strong>${title}</strong><br>${lat.toFixed(6)}, ${lng.toFixed(6)}`).openPopup();
        
        // Centralizar mapa na nova localização
        map.setView([lat, lng], 15);
    }
}

// Função para atualizar informações do mapa
function updateMapInfo(lat, lng, address = '') {
    document.getElementById('map-coordinates').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    document.getElementById('map-address').textContent = address || 'Endereço não disponível';
}

// Função para atualizar mapa quando localização for detectada
function updateMapWithLocation(lat, lng, address = '') {
    currentLat = lat;
    currentLng = lng;
    addMarker(lat, lng, 'Nova localização detectada');
    updateMapInfo(lat, lng, address);
}
</script>
@endsection
