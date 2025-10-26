<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas, não no controller
    }

    /**
     * Show the chat interface with a specific user
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Update current user's last_seen immediately when accessing chat
        $currentUser->update(['last_seen' => now()]);
        
        // Check if users are matched
        $match = UserMatch::where(function($query) use ($currentUser, $user) {
            $query->where('user1_id', $currentUser->id)->where('user2_id', $user->id);
        })->orWhere(function($query) use ($currentUser, $user) {
            $query->where('user1_id', $user->id)->where('user2_id', $currentUser->id);
        })->where('status', 'accepted')->first();

        if (!$match) {
            return redirect()->route('matching.matches')->with('error', 'Você não tem match com este usuário.');
        }

        // Get conversation messages
        $messages = Message::betweenUsers($currentUser->id, $user->id)
            ->with(['sender', 'receiver'])
            ->recent(50)
            ->get()
            ->reverse();

        // Mark messages as read
        Message::where('receiver_id', $currentUser->id)
            ->where('sender_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return view('chat.show', compact('user', 'messages', 'match'));
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        // Update current user's last_seen when sending message
        $currentUser->update(['last_seen' => now()]);
        
        // Check if users are matched
        $match = UserMatch::where(function($query) use ($currentUser, $user) {
            $query->where('user1_id', $currentUser->id)->where('user2_id', $user->id);
        })->orWhere(function($query) use ($currentUser, $user) {
            $query->where('user1_id', $user->id)->where('user2_id', $currentUser->id);
        })->where('status', 'accepted')->first();

        if (!$match) {
            return response()->json(['error' => 'Você não tem match com este usuário.'], 403);
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'message_type' => 'nullable|in:text,image,file,audio',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'audio' => 'nullable|file|mimes:webm,mp3,wav,ogg|max:10240', // 10MB max for audio
        ], [
            'image.image' => 'O arquivo deve ser uma imagem válida.',
            'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif ou webp.',
            'image.max' => 'A imagem deve ter no máximo 5MB.',
            'audio.mimes' => 'O áudio deve ser do tipo: webm, mp3, wav ou ogg.',
            'audio.max' => 'O áudio deve ter no máximo 10MB.',
        ]);

        $messageType = $request->message_type ?? 'text';
        $messageContent = $request->message;
        $attachmentPath = null;

        // Handle image upload
        if ($request->hasFile('image') && $messageType === 'image') {
            \Log::info('Starting image upload process in chat', [
                'sender_id' => $currentUser->id,
                'receiver_id' => $user->id,
                'has_file' => $request->hasFile('image'),
                'message_type' => $messageType
            ]);
            
            $image = $request->file('image');
            
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Store image in storage/app/public/chat-images
            $path = $image->storeAs('chat-images', $filename, 'public');
            $attachmentPath = $path;
            
            \Log::info('Image stored successfully in chat', [
                'path' => $path,
                'attachment_path' => $attachmentPath,
                'file_exists' => \Storage::disk('public')->exists($path)
            ]);
            
            // Update message content to show image info
            $messageContent = $messageContent ?: '📷 Imagem enviada';
        }
        
        // Handle audio upload
        if ($request->hasFile('audio') && $messageType === 'audio') {
            $audio = $request->file('audio');
            
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $audio->getClientOriginalExtension();
            
            // Store audio in storage/app/public/chat-audio
            $path = $audio->storeAs('chat-audio', $filename, 'public');
            $attachmentPath = $path;
            
            // Update message content to show audio info
            $messageContent = $messageContent ?: '🎤 Áudio enviado';
        }

        $messageData = [
            'sender_id' => $currentUser->id,
            'receiver_id' => $user->id,
            'message' => $messageContent,
            'message_type' => $messageType,
            'attachment_path' => $attachmentPath,
        ];

        $message = Message::create($messageData);
        $message->load(['sender', 'receiver']);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Get conversation messages (AJAX)
     */
    public function getMessages(User $user, Request $request)
    {
        $currentUser = Auth::user();
        $lastMessageId = $request->get('last_message_id', 0);

        $messages = Message::betweenUsers($currentUser->id, $user->id)
            ->with(['sender', 'receiver'])
            ->where('id', '>', $lastMessageId)
            ->recent(20)
            ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(User $user)
    {
        $currentUser = Auth::user();
        
        Message::where('receiver_id', $currentUser->id)
            ->where('sender_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get all conversations for the current user
     */
    public function conversations()
    {
        $currentUser = Auth::user();
        
        // Get all users that have exchanged messages with current user
        $conversations = Message::where('sender_id', $currentUser->id)
            ->orWhere('receiver_id', $currentUser->id)
            ->with(['sender', 'receiver'])
            ->get()
            ->groupBy(function($message) use ($currentUser) {
                return $message->sender_id === $currentUser->id ? 
                    $message->receiver_id : $message->sender_id;
            })
            ->map(function($messages, $userId) use ($currentUser) {
                $otherUser = $messages->first()->sender_id === $currentUser->id ? 
                    $messages->first()->receiver : $messages->first()->sender;
                
                $lastMessage = $messages->sortByDesc('created_at')->first();
                $unreadCount = $messages->where('receiver_id', $currentUser->id)
                    ->where('is_read', false)
                    ->count();

                return [
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'updated_at' => $lastMessage->created_at
                ];
            })
            ->sortByDesc('updated_at')
            ->values();

        return view('chat.conversations', compact('conversations'));
    }

    /**
     * Delete a message
     */
    public function deleteMessage(Message $message)
    {
        $currentUser = Auth::user();
        
        // Only sender can delete their own messages
        if ($message->sender_id !== $currentUser->id) {
            return response()->json(['error' => 'Você não pode deletar esta mensagem.'], 403);
        }

        // Delete attachment if exists
        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get unread message count
     */
    public function unreadCount()
    {
        $currentUser = Auth::user();
        
        $unreadCount = Message::where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * Clear chat history with a user
     */
    public function clearChat(User $user)
    {
        $currentUser = Auth::user();
        
        // Delete all messages between current user and target user
        Message::where(function($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $user->id);
        })->orWhere(function($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $currentUser->id);
        })->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Block a user
     */
    public function blockUser(User $user)
    {
        $currentUser = Auth::user();
        
        // Create or update block record
        $currentUser->blockedUsers()->syncWithoutDetaching([$user->id]);
        
        // Delete all messages between users
        Message::where(function($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $user->id);
        })->orWhere(function($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $currentUser->id);
        })->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Report a user
     */
    public function reportUser(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        // Create report record
        $currentUser->reports()->create([
            'reported_user_id' => $user->id,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Archive a chat
     */
    public function archiveChat(User $user)
    {
        $currentUser = Auth::user();
        
        // Mark conversation as archived
        $conversation = Conversation::where(function($query) use ($currentUser, $user) {
            $query->where('user1_id', $currentUser->id)
                  ->where('user2_id', $user->id);
        })->orWhere(function($query) use ($currentUser, $user) {
            $query->where('user1_id', $user->id)
                  ->where('user2_id', $currentUser->id);
        })->first();

        if ($conversation) {
            $conversation->update(['is_archived' => true]);
        }

        return response()->json(['success' => true]);
    }
}