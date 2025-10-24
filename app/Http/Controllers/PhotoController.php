<?php

namespace App\Http\Controllers;

use App\Models\UserPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas, não no controller
    }

    /**
     * Upload a new photo.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB max
        ]);

        // Check photo limit (max 6 photos for free users, unlimited for premium)
        $photoCount = $user->photos()->count();
        $maxPhotos = $user->hasActivePremiumSubscription() ? 20 : 6;

        if ($photoCount >= $maxPhotos) {
            return redirect()->back()->with('error', 'Limite de fotos atingido. ' . 
                ($user->hasActivePremiumSubscription() ? 'Contate o suporte.' : 'Faça upgrade para premium para mais fotos.'));
        }

        // Store photo
        $path = $request->file('photo')->store('user-photos', 'public');
        
        // Get next sort order
        $nextOrder = $user->photos()->max('sort_order') + 1;

        $photo = $user->photos()->create([
            'photo_path' => $path,
            'photo_url' => Storage::disk('public')->url($path),
            'sort_order' => $nextOrder,
            'is_approved' => false, // Require approval
        ]);

        return redirect()->back()->with('success', 'Foto enviada com sucesso! Aguardando aprovação.')->with('active_tab', 'photos');
    }

    /**
     * Set a photo as primary.
     */
    public function setPrimary(UserPhoto $photo)
    {
        $user = Auth::user();

        // Check if user owns this photo
        if ($photo->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        // Remove primary from all other photos
        $user->photos()->update(['is_primary' => false]);

        // Set this photo as primary
        $photo->update(['is_primary' => true]);

        return redirect()->back()->with('success', 'Foto principal atualizada!')->with('active_tab', 'photos');
    }

    /**
     * Update photo order.
     */
    public function updateOrder(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'photos' => ['required', 'array'],
            'photos.*' => ['integer', 'exists:user_photos,id'],
        ]);

        foreach ($request->photos as $index => $photoId) {
            $photo = $user->photos()->find($photoId);
            if ($photo) {
                $photo->update(['sort_order' => $index + 1]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete a photo.
     */
    public function destroy(UserPhoto $photo)
    {
        $user = Auth::user();

        // Check if user owns this photo
        if ($photo->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        // Delete from database
        $photo->delete();

        return redirect()->back()->with('success', 'Foto removida com sucesso!')->with('active_tab', 'photos');
    }
}
