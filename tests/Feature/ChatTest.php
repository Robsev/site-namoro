<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Message;
use App\Models\UserMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test user can send message to matched user
     */
    public function test_user_can_send_message_to_matched_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);

        $this->actingAs($user1);

        $messageData = [
            'message' => 'Hello! How are you?',
            'message_type' => 'text'
        ];

        $response = $this->post("/chat/send/{$user2->id}", $messageData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'Hello! How are you?'
        ]);
    }

    /**
     * Test user cannot send message to non-matched user
     */
    public function test_user_cannot_send_message_to_non_matched_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1);

        $messageData = [
            'message' => 'Hello! How are you?',
            'message_type' => 'text'
        ];

        $response = $this->post("/chat/send/{$user2->id}", $messageData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('messages', [
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id
        ]);
    }

    /**
     * Test user can view chat with matched user
     */
    public function test_user_can_view_chat_with_matched_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);

        $this->actingAs($user1);

        $response = $this->get("/chat/{$user2->id}");

        $response->assertStatus(200);
        $response->assertSee('Conversar');
    }

    /**
     * Test user cannot view chat with non-matched user
     */
    public function test_user_cannot_view_chat_with_non_matched_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1);

        $response = $this->get("/chat/{$user2->id}");

        $response->assertRedirect('/matches');
    }

    /**
     * Test message is marked as read when viewed
     */
    public function test_message_is_marked_as_read_when_viewed()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);

        // Create a message
        Message::create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'Hello!',
            'is_read' => false
        ]);

        $this->actingAs($user2);

        $response = $this->get("/chat/{$user1->id}");

        $response->assertStatus(200);
        
        $message = Message::where('sender_id', $user1->id)
            ->where('receiver_id', $user2->id)
            ->first();

        $this->assertTrue($message->is_read);
        $this->assertNotNull($message->read_at);
    }

    /**
     * Test user can get conversation messages
     */
    public function test_user_can_get_conversation_messages()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);

        // Create messages
        Message::create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'Hello!'
        ]);

        Message::create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Hi there!'
        ]);

        $this->actingAs($user1);

        $response = $this->get("/chat/messages/{$user2->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'messages');
    }

    /**
     * Test user can delete their own messages
     */
    public function test_user_can_delete_their_own_messages()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);

        // Create a message
        $message = Message::create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'Hello!'
        ]);

        $this->actingAs($user1);

        $response = $this->delete("/chat/message/{$message->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('messages', [
            'id' => $message->id
        ]);
    }

    /**
     * Test user cannot delete other user's messages
     */
    public function test_user_cannot_delete_other_users_messages()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);

        // Create a message
        $message = Message::create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Hello!'
        ]);

        $this->actingAs($user1);

        $response = $this->delete("/chat/message/{$message->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('messages', [
            'id' => $message->id
        ]);
    }

    /**
     * Test conversations page shows user conversations
     */
    public function test_conversations_page_shows_user_conversations()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);

        // Create a message
        Message::create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'Hello!'
        ]);

        $this->actingAs($user1);

        $response = $this->get('/chat');

        $response->assertStatus(200);
        $response->assertSee($user2->first_name);
    }

    /**
     * Test unread message count
     */
    public function test_unread_message_count()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);

        // Create unread messages
        Message::create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Hello!',
            'is_read' => false
        ]);

        Message::create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'How are you?',
            'is_read' => false
        ]);

        $this->actingAs($user1);

        $response = $this->get('/chat/unread-count');

        $response->assertStatus(200);
        $response->assertJson(['unread_count' => 2]);
    }

    /**
     * Test message validation
     */
    public function test_message_validation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);

        $this->actingAs($user1);

        // Test empty message
        $response = $this->post("/chat/send/{$user2->id}", [
            'message' => '',
            'message_type' => 'text'
        ]);

        $response->assertSessionHasErrors('message');

        // Test message too long
        $response = $this->post("/chat/send/{$user2->id}", [
            'message' => str_repeat('a', 1001),
            'message_type' => 'text'
        ]);

        $response->assertSessionHasErrors('message');
    }

    /**
     * Test file upload in chat
     */
    public function test_file_upload_in_chat()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);

        $this->actingAs($user1);

        // Create a fake file
        $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');

        $response = $this->post("/chat/send/{$user2->id}", [
            'message' => 'Check this out!',
            'message_type' => 'image',
            'attachment' => $file
        ]);

        $response->assertStatus(200);
        
        $message = Message::where('sender_id', $user1->id)
            ->where('receiver_id', $user2->id)
            ->first();

        $this->assertEquals('image', $message->message_type);
        $this->assertNotNull($message->attachment_path);
    }
}