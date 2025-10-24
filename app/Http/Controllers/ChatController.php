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
            'message' => 'required|string|max:1000',
            'message_type' => 'in:text,image,file',
            'attachment' => 'nullable|file|max:10240' // 10MB max
        ]);

        $messageData = [
            'sender_id' => $currentUser->id,
            'receiver_id' => $user->id,
            'message' => $request->message,
            'message_type' => $request->message_type ?? 'text',
        ];

        // Handle file uploads
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('chat-attachments', 'public');
            $messageData['attachment_path'] = $path;
            $messageData['message_type'] = $file->getMimeType() === 'image/jpeg' || 
                                         $file->getMimeType() === 'image/png' || 
                                         $file->getMimeType() === 'image/gif' ? 'image' : 'file';
        }

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
}