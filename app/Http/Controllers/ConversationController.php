<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas
    }

    /**
     * Display a listing of conversations for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        
        $conversations = Conversation::forUser($user->id)
            ->active()
            ->with(['user1', 'user2', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return view('conversations.index', compact('conversations'));
    }

    /**
     * Show a specific conversation.
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        
        // Check if user is part of this conversation
        if (!$conversation->hasUser($user->id)) {
            abort(403, 'Você não tem acesso a esta conversa.');
        }

        // Mark messages as read
        $conversation->markAsReadForUser($user->id);

        // Get messages for this conversation
        $messages = $conversation->messages()
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Debug log
        \Log::info('Messages loaded', [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'messages_count' => $messages->count(),
            'messages' => $messages->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at
                ];
            })
        ]);

        // Get the other user
        $otherUser = $conversation->getOtherUser($user->id);

        return view('conversations.show', compact('conversation', 'messages', 'otherUser'));
    }

    /**
     * Get messages for a conversation (API endpoint).
     */
    public function getMessages(Conversation $conversation)
    {
        $user = Auth::user();
        
        // Check if user is part of this conversation
        if (!$conversation->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get messages for this conversation
        $messages = $conversation->messages()
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'message' => $msg->message,
                    'message_type' => $msg->message_type,
                    'attachment_path' => $msg->attachment_path,
                    'created_at' => $msg->created_at,
                    'sender' => $msg->sender
                ];
            })
        ]);
    }

    /**
     * Start a new conversation with a user.
     */
    public function start(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'user_id' => 'required|exists:users,id|different:' . $user->id,
        ]);

        $otherUserId = $request->user_id;

        // Check if users are matched
        $match = \App\Models\UserMatch::where(function($query) use ($user, $otherUserId) {
            $query->where('user1_id', $user->id)->where('user2_id', $otherUserId);
        })->orWhere(function($query) use ($user, $otherUserId) {
            $query->where('user1_id', $otherUserId)->where('user2_id', $user->id);
        })->where('status', 'accepted')->first();

        if (!$match) {
            return redirect()->back()->with('error', 'Você só pode conversar com usuários que você deu match.');
        }

        // Check if conversation already exists
        $conversation = Conversation::where(function($query) use ($user, $otherUserId) {
            $query->where('user1_id', $user->id)->where('user2_id', $otherUserId);
        })->orWhere(function($query) use ($user, $otherUserId) {
            $query->where('user1_id', $otherUserId)->where('user2_id', $user->id);
        })->first();

        if (!$conversation) {
            // Create new conversation
            $conversation = Conversation::create([
                'user1_id' => $user->id,
                'user2_id' => $otherUserId,
                'is_active' => true,
            ]);
        }

        return redirect()->route('conversations.show', $conversation);
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        try {
            $user = Auth::user();
            
            // Check if user is part of this conversation
            if (!$conversation->hasUser($user->id)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Você não tem acesso a esta conversa.'], 403);
                }
                abort(403, 'Você não tem acesso a esta conversa.');
            }

            $request->validate([
                'message' => 'nullable|string|max:1000',
                'message_type' => 'nullable|in:text,image,file',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            ], [
                'image.image' => 'O arquivo deve ser uma imagem válida.',
                'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif ou webp.',
                'image.max' => 'A imagem deve ter no máximo 5MB.',
            ]);

        $otherUser = $conversation->getOtherUser($user->id);
        $messageType = $request->message_type ?? 'text';
        $messageContent = $request->message;
        $attachmentPath = null;

        // Handle image upload
        if ($request->hasFile('image') && $messageType === 'image') {
            \Log::info('Starting image upload process', [
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'has_file' => $request->hasFile('image'),
                'message_type' => $messageType
            ]);
            
            $image = $request->file('image');
            
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            \Log::info('Image file details', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'extension' => $image->getClientOriginalExtension()
            ]);
            
            // Store image in storage/app/public/chat-images
            $path = $image->storeAs('chat-images', $filename, 'public');
            $attachmentPath = $path;
            
            \Log::info('Image stored successfully', [
                'path' => $path,
                'attachment_path' => $attachmentPath,
                'file_exists' => \Storage::disk('public')->exists($path)
            ]);
            
            // Update message content to show image info
            $messageContent = $messageContent ?: '📷 Imagem enviada';
        }

        // Create message
        \Log::info('Creating message with data', [
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $otherUser->id,
            'message' => $messageContent,
            'message_type' => $messageType,
            'attachment_path' => $attachmentPath,
        ]);
        
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $otherUser->id,
            'message' => $messageContent,
            'message_type' => $messageType,
            'attachment_path' => $attachmentPath,
            'is_read' => false,
        ]);
        
        \Log::info('Message created successfully', [
            'message_id' => $message->id,
            'message_type' => $message->message_type,
            'attachment_path' => $message->attachment_path,
        ]);

        // Debug log
        \Log::info('Message created', [
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $otherUser->id,
            'message' => $messageContent,
            'message_type' => $messageType,
            'attachment_path' => $attachmentPath,
            'user_name' => $user->name,
            'other_user_name' => $otherUser->name,
            'is_ajax' => $request->ajax(),
        ]);

        // Update conversation last message time
        $conversation->update([
            'last_message_at' => now(),
        ]);

        // Send notification to receiver
        $otherUser->notify(new \App\Notifications\NewMessage($message));

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message->load('sender'),
                ]);
            }

            return redirect()->back()->with('success', 'Mensagem enviada com sucesso!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in sendMessage', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
                'conversation_id' => $conversation->id
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação: ' . implode(', ', array_flatten($e->errors())),
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors());
            
        } catch (\Exception $e) {
            \Log::error('Error in sendMessage', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'conversation_id' => $conversation->id
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Erro ao enviar mensagem: ' . $e->getMessage());
        }
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(Conversation $conversation)
    {
        $user = Auth::user();
        
        if (!$conversation->hasUser($user->id)) {
            abort(403, 'Você não tem acesso a esta conversa.');
        }

        $conversation->markAsReadForUser($user->id);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread message count for the user.
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $unreadCount = Conversation::forUser($user->id)
            ->active()
            ->get()
            ->sum(function($conversation) use ($user) {
                return $conversation->getUnreadCountForUser($user->id);
            });

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * Archive a conversation.
     */
    public function archive(Conversation $conversation)
    {
        $user = Auth::user();
        
        if (!$conversation->hasUser($user->id)) {
            abort(403, 'Você não tem acesso a esta conversa.');
        }

        $conversation->update(['is_active' => false]);

        return redirect()->route('conversations.index')->with('success', 'Conversa arquivada com sucesso!');
    }

    /**
     * Unarchive a conversation.
     */
    public function unarchive(Conversation $conversation)
    {
        $user = Auth::user();
        
        if (!$conversation->hasUser($user->id)) {
            abort(403, 'Você não tem acesso a esta conversa.');
        }

        $conversation->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Conversa restaurada com sucesso!');
    }
}