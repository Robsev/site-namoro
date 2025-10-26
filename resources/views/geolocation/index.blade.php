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
                    <p><strong>Localização:</strong> <span id="map-address">
                        @if($user->hasGeolocation())
                            {{ $user->formatted_location ?: 'Localização não disponível' }}
                        @else
                            -
                        @endif
                    </span></p>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Endereço completo é exibido apenas para verificação - não é armazenado por questões de privacidade
                    </p>
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

                <!-- Manual Coordinates (Read-only for security) -->
                <div class="space-y-4">
                    <h3 class="text-sm font-medium text-gray-700">Informações de Localização:</h3>
                    
                    <form id="manualLocationForm" action="{{ route('location.update') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Latitude</label>
                                <input type="number" 
                                       name="latitude" 
                                       id="latitude"
                                       step="any" 
                                       value="{{ $user->latitude }}"
                                       readonly
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Longitude</label>
                                <input type="number" 
                                       name="longitude" 
                                       id="longitude"
                                       step="any" 
                                       value="{{ $user->longitude }}"
                                       readonly
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                            </div>
                        </div>
                        

                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">País</label>
                            <input type="text" 
                                   name="country" 
                                   id="country"
                                   value="{{ $user->country }}"
                                   readonly
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                                <input type="text" 
                                       name="state" 
                                       id="state"
                                       value="{{ $user->state }}"
                                       readonly
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Cidade</label>
                                <input type="text" 
                                       name="city" 
                                       id="city"
                                       value="{{ $user->city }}"
                                       readonly
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bairro</label>
                            <input type="text" 
                                   name="neighborhood" 
                                   id="neighborhood"
                                   value="{{ $user->neighborhood }}"
                                   readonly
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <button type="submit" 
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Salvar Localização
                            </button>
                            <button type="button" 
                                    onclick="clearLocation()"
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">
                                <i class="fas fa-trash mr-2"></i>
                                Limpar Localização
                            </button>
                        </div>
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
            
            // Tentar obter localização com melhor precisão
            let attempts = 0;
            const maxAttempts = 3;
            
            function tryGetLocation() {
                attempts++;
                console.log(`Tentativa ${attempts}/${maxAttempts} de obter localização`);
                
                navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    console.log('Localização detectada:', {
                        latitude: lat,
                        longitude: lng,
                        accuracy: position.coords.accuracy + 'm',
                        altitude: position.coords.altitude,
                        altitudeAccuracy: position.coords.altitudeAccuracy,
                        heading: position.coords.heading,
                        speed: position.coords.speed,
                        timestamp: new Date(position.timestamp)
                    });
                    
                    // Verificar precisão
                    if (position.coords.accuracy > 100 && attempts < maxAttempts) {
                        console.warn('Precisão baixa detectada:', position.coords.accuracy + 'm', '- Tentando novamente...');
                        setTimeout(tryGetLocation, 2000); // Tentar novamente em 2 segundos
                        return;
                    }
                    
                    console.log('Localização aceita com precisão:', position.coords.accuracy + 'm');
                    
                    // Atualizar mapa IMEDIATAMENTE
                    updateMapWithLocation(lat, lng, 'Localização detectada (Precisão: ' + Math.round(position.coords.accuracy) + 'm)');
                    
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
                            if (data.location.neighborhood) document.querySelector('input[name="neighborhood"]').value = data.location.neighborhood;
                            
                            // Atualizar mapa com endereço completo
                            updateMapWithLocation(lat, lng, data.location.address || 'Localização detectada');
                            
                            // Mostrar mensagem de sucesso
                            showNotification('Localização detectada! Salvando automaticamente...', 'success');
                            
                            // Auto-submit form após um pequeno delay para mostrar a mensagem
                            setTimeout(() => {
                                manualForm.submit();
                            }, 1500);
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
                    timeout: 20000,
                    maximumAge: 0, // Sempre obter nova localização
                    watchPosition: false
                }
                );
            }
            
            // Iniciar primeira tentativa
            tryGetLocation();
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
        // Adicionar marcador do Google Maps (usando API moderna)
        if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
            // Usar AdvancedMarkerElement (recomendado)
            marker = new google.maps.marker.AdvancedMarkerElement({
                position: { lat: lat, lng: lng },
                map: map,
                title: title
            });
        } else {
            // Fallback para Marker clássico
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
        }
        
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
    const coordinatesEl = document.getElementById('map-coordinates');
    const addressEl = document.getElementById('map-address');
    
    if (coordinatesEl) {
        coordinatesEl.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
    
    if (addressEl) {
        addressEl.textContent = address || 'Endereço não disponível';
    }
}

// Função para atualizar mapa quando localização for detectada
function updateMapWithLocation(lat, lng, address = '') {
    currentLat = lat;
    currentLng = lng;
    addMarker(lat, lng, 'Nova localização detectada');
    updateMapInfo(lat, lng, address);
}

// Função para limpar localização
function clearLocation() {
    if (!confirm('Tem certeza que deseja limpar todos os dados de localização? Este processo não pode ser desfeito.')) {
        return;
    }
    
    // Resetar variáveis globais
    currentLat = null;
    currentLng = null;
    
    // Limpar campos do formulário
    document.getElementById('latitude').value = '';
    document.getElementById('longitude').value = '';
    document.getElementById('country').value = '';
    document.getElementById('state').value = '';
    document.getElementById('city').value = '';
    document.getElementById('neighborhood').value = '';
    
    // Remover marcador do mapa se existir
    const mapAPI = detectMapAPI();
    if (marker && map) {
        if (mapAPI === 'google') {
            marker.setMap(null);
        } else if (mapAPI === 'leaflet') {
            map.removeLayer(marker);
        }
    }
    
    // Atualizar informação no mapa
    const mapAddress = document.getElementById('map-address');
    if (mapAddress) {
        mapAddress.textContent = '-';
    }
    
    // Submeter formulário vazio
    document.getElementById('manualLocationForm').submit();
}

// Função para mostrar notificações
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'info' ? 'bg-blue-500 text-white' :
        'bg-gray-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
