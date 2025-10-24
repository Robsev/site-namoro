<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class GeolocationController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas, não no controller
    }

    /**
     * Show geolocation settings page
     */
    public function index()
    {
        $user = Auth::user();
        return view('geolocation.index', compact('user'));
    }

    /**
     * Update user's geolocation data
     */
    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        
        // Get additional location data from coordinates if not provided
        if (!$request->filled('city') || !$request->filled('state')) {
            $locationData = $this->getLocationFromCoordinates(
                $request->latitude, 
                $request->longitude
            );
        } else {
            $locationData = [];
        }

        $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city ?: $locationData['city'] ?? null,
            'state' => $request->state ?: $locationData['state'] ?? null,
            'country' => $request->country ?: $locationData['country'] ?? null,
            'neighborhood' => $request->neighborhood ?: $locationData['neighborhood'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Localização atualizada com sucesso!');
    }

    /**
     * Get location data from coordinates using reverse geocoding
     */
    private function getLocationFromCoordinates($latitude, $longitude)
    {
        try {
            \Log::info("Tentando geocoding reverso para: {$latitude}, {$longitude}");
            
            // Using OpenStreetMap Nominatim API (free)
            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'AmigosParaSempre/1.0 (https://amigosparasempre.com)',
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'json',
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'addressdetails' => 1,
                    'accept-language' => 'pt-BR,pt,en',
                    'zoom' => 18, // Mais detalhes
                ]);

            \Log::info("Resposta da API: " . $response->status());
            
            if ($response->successful()) {
                $data = $response->json();
                \Log::info("Dados recebidos: " . json_encode($data));
                
                $address = $data['address'] ?? [];
                
                $result = [
                    'address' => $data['display_name'] ?? null,
                    'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['municipality'] ?? null,
                    'state' => $address['state'] ?? $address['region'] ?? $address['province'] ?? null,
                    'country' => $address['country'] ?? null,
                    'neighborhood' => $address['suburb'] ?? $address['neighbourhood'] ?? $address['quarter'] ?? 
                                    $address['city_district'] ?? $address['district'] ?? $address['county'] ?? 
                                    $address['hamlet'] ?? $address['village'] ?? null,
                ];
                
                \Log::info("Resultado processado: " . json_encode($result));
                return $result;
            } else {
                \Log::warning("API retornou erro: " . $response->status() . " - " . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error('Geocoding failed: ' . $e->getMessage());
        }

        // Fallback: tentar com Google Geocoding se disponível
        return $this->getLocationFromGoogleMaps($latitude, $longitude);
    }

    /**
     * Fallback method using Google Geocoding API
     */
    private function getLocationFromGoogleMaps($latitude, $longitude)
    {
        try {
            $apiKey = env('GOOGLE_MAPS_API_KEY');
            if (!$apiKey) {
                \Log::info('Google Maps API key não configurada, usando apenas OpenStreetMap');
                return [];
            }

            \Log::info("Tentando Google Geocoding para: {$latitude}, {$longitude}");
            
            $response = Http::timeout(15)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$latitude},{$longitude}",
                'key' => $apiKey,
                'language' => 'pt-BR',
                'region' => 'BR',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $result = $data['results'][0];
                    $addressComponents = $result['address_components'];
                    
                    $location = [];
                    foreach ($addressComponents as $component) {
                        $types = $component['types'];
                        if (in_array('locality', $types)) {
                            $location['city'] = $component['long_name'];
                        } elseif (in_array('administrative_area_level_1', $types)) {
                            $location['state'] = $component['long_name'];
                        } elseif (in_array('country', $types)) {
                            $location['country'] = $component['long_name'];
                        } elseif (in_array('postal_code', $types)) {
                            $location['postal_code'] = $component['long_name'];
                        } elseif (in_array('sublocality', $types) || in_array('sublocality_level_1', $types)) {
                            $location['neighborhood'] = $component['long_name'];
                        } elseif (in_array('route', $types)) {
                            $location['road'] = $component['long_name'];
                        } elseif (in_array('street_number', $types)) {
                            $location['house_number'] = $component['long_name'];
                        }
                    }
                    
                    $location['address'] = $result['formatted_address'];
                    \Log::info("Google Geocoding resultado: " . json_encode($location));
                    return $location;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Google Geocoding failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Search for locations by name
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3|max:100',
        ]);

        try {
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'format' => 'json',
                'q' => $request->query,
                'addressdetails' => 1,
                'limit' => 10,
                'accept-language' => 'pt-BR,pt,en',
                'countrycodes' => 'br', // Focus on Brazil
            ]);

            if ($response->successful()) {
                $results = $response->json();
                
                $locations = collect($results)->map(function($item) {
                    $address = $item['address'] ?? [];
                    return [
                        'display_name' => $item['display_name'],
                        'latitude' => (float) $item['lat'],
                        'longitude' => (float) $item['lon'],
                        'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? null,
                        'state' => $address['state'] ?? $address['region'] ?? null,
                        'country' => $address['country'] ?? null,
                        'postal_code' => $address['postcode'] ?? null,
                    ];
                });

                return response()->json($locations);
            }
        } catch (\Exception $e) {
            \Log::warning('Location search failed: ' . $e->getMessage());
        }

        return response()->json([]);
    }

    /**
     * Get current location using browser geolocation
     */
    public function getCurrentLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $user = Auth::user();
        
        // Get location data from coordinates
        $locationData = $this->getLocationFromCoordinates(
            $request->latitude, 
            $request->longitude
        );

        return response()->json([
            'success' => true,
            'location' => $locationData,
            'coordinates' => [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        ]);
    }

    /**
     * Update location from search result
     */
    public function updateFromSearch(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        
        $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Localização atualizada com sucesso!'
        ]);
    }
}
