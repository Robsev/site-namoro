<?php

namespace App\Http\Controllers;

use App\Models\UserPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PhotoModerationResult;

class AdminPhotoController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes, not in controller
    }

    /**
     * Display a listing of photos pending moderation.
     */
    public function index(Request $request)
    {
        $query = UserPhoto::with(['user'])
            ->where('moderation_status', 'pending')
            ->orderBy('created_at', 'desc');

        // Filter by user if specified
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $photos = $query->paginate(20);

        // Get users for filter dropdown
        $users = User::whereHas('photos', function($query) {
            $query->where('moderation_status', 'pending');
        })->select('id', 'name', 'email')->get();

        return view('admin.photos.index', compact('photos', 'users'));
    }

    /**
     * Show a specific photo for moderation.
     */
    public function show(UserPhoto $photo)
    {
        $photo->load(['user']);
        
        // Get other photos from the same user for context
        $userPhotos = UserPhoto::where('user_id', $photo->user_id)
            ->where('id', '!=', $photo->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.photos.show', compact('photo', 'userPhotos'));
    }

    /**
     * Approve a photo.
     */
    public function approve(Request $request, UserPhoto $photo)
    {
        $request->validate([
            'moderation_notes' => 'nullable|string|max:1000',
        ]);

        $photo->update([
            'moderation_status' => 'approved',
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
            'moderation_notes' => $request->moderation_notes,
        ]);

        // Send notification to user
        $photo->user->notify(new PhotoModerationResult($photo, 'approved'));

        return redirect()->route('admin.photos.index')
            ->with('success', 'Foto aprovada com sucesso!');
    }

    /**
     * Reject a photo.
     */
    public function reject(Request $request, UserPhoto $photo)
    {
        $request->validate([
            'moderation_notes' => 'required|string|max:1000',
            'reason' => 'required|in:inappropriate,low_quality,not_clear,other',
        ]);

        $photo->update([
            'moderation_status' => 'rejected',
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
            'moderation_notes' => $request->moderation_notes,
        ]);

        // Send notification to user
        $photo->user->notify(new PhotoModerationResult($photo, 'rejected'));

        return redirect()->route('admin.photos.index')
            ->with('success', 'Foto rejeitada com sucesso!');
    }

    /**
     * Bulk approve multiple photos.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'photo_ids' => 'required|array',
            'photo_ids.*' => 'exists:user_photos,id',
            'moderation_notes' => 'nullable|string|max:1000',
        ]);

        $photos = UserPhoto::whereIn('id', $request->photo_ids)
            ->where('moderation_status', 'pending')
            ->get();

        foreach ($photos as $photo) {
            $photo->update([
                'moderation_status' => 'approved',
                'moderated_by' => auth()->id(),
                'moderated_at' => now(),
                'moderation_notes' => $request->moderation_notes,
            ]);

            // Send notification to user
            $photo->user->notify(new PhotoModerationResult($photo, 'approved'));
        }

        return redirect()->route('admin.photos.index')
            ->with('success', count($photos) . ' fotos aprovadas com sucesso!');
    }

    /**
     * Bulk reject multiple photos.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'photo_ids' => 'required|array',
            'photo_ids.*' => 'exists:user_photos,id',
            'moderation_notes' => 'required|string|max:1000',
            'reason' => 'required|in:inappropriate,low_quality,not_clear,other',
        ]);

        $photos = UserPhoto::whereIn('id', $request->photo_ids)
            ->where('moderation_status', 'pending')
            ->get();

        foreach ($photos as $photo) {
            $photo->update([
                'moderation_status' => 'rejected',
                'moderated_by' => auth()->id(),
                'moderated_at' => now(),
                'moderation_notes' => $request->moderation_notes,
            ]);

            // Send notification to user
            $photo->user->notify(new PhotoModerationResult($photo, 'rejected'));
        }

        return redirect()->route('admin.photos.index')
            ->with('success', count($photos) . ' fotos rejeitadas com sucesso!');
    }

    /**
     * Get moderation statistics.
     */
    public function statistics()
    {
        $stats = [
            'pending' => UserPhoto::where('moderation_status', 'pending')->count(),
            'approved' => UserPhoto::where('moderation_status', 'approved')->count(),
            'rejected' => UserPhoto::where('moderation_status', 'rejected')->count(),
            'total' => UserPhoto::count(),
        ];

        // Recent activity
        $recentActivity = UserPhoto::with(['user', 'moderator'])
            ->whereNotNull('moderated_at')
            ->orderBy('moderated_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.photos.statistics', compact('stats', 'recentActivity'));
    }

    /**
     * Delete a photo permanently (admin only).
     */
    public function destroy(UserPhoto $photo)
    {
        // Delete the actual file
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        return redirect()->route('admin.photos.index')
            ->with('success', 'Foto removida permanentemente!');
    }
}
