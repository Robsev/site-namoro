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
        $this->middleware('auth');
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
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
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
            'address' => $request->address ?: $locationData['address'] ?? null,
            'city' => $request->city ?: $locationData['city'] ?? null,
            'state' => $request->state ?: $locationData['state'] ?? null,
            'country' => $request->country ?: $locationData['country'] ?? null,
            'postal_code' => $request->postal_code ?: $locationData['postal_code'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Localização atualizada com sucesso!');
    }

    /**
     * Get location data from coordinates using reverse geocoding
     */
    private function getLocationFromCoordinates($latitude, $longitude)
    {
        try {
            // Using OpenStreetMap Nominatim API (free)
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'addressdetails' => 1,
                'accept-language' => 'pt-BR,pt,en',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $address = $data['address'] ?? [];
                
                return [
                    'address' => $data['display_name'] ?? null,
                    'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? null,
                    'state' => $address['state'] ?? $address['region'] ?? null,
                    'country' => $address['country'] ?? null,
                    'postal_code' => $address['postcode'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            \Log::warning('Geocoding failed: ' . $e->getMessage());
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
