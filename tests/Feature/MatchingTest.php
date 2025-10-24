<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserMatch;
use App\Models\UserProfile;
use App\Models\MatchingPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MatchingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test user can like another user
     */
    public function test_user_can_like_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1);

        $response = $this->post("/matching/like/{$user2->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_matches', [
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'pending'
        ]);
    }

    /**
     * Test user cannot like themselves
     */
    public function test_user_cannot_like_themselves()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post("/matching/like/{$user->id}");

        $response->assertStatus(400);
        $this->assertDatabaseMissing('user_matches', [
            'user1_id' => $user->id,
            'user2_id' => $user->id
        ]);
    }

    /**
     * Test user can pass on another user
     */
    public function test_user_can_pass_on_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1);

        $response = $this->post("/matching/pass/{$user2->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_matches', [
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'rejected'
        ]);
    }

    /**
     * Test user can super like another user
     */
    public function test_user_can_super_like_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1);

        $response = $this->post("/matching/super-like/{$user2->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_matches', [
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'is_super_like' => true
        ]);
    }

    /**
     * Test compatibility score calculation
     */
    public function test_compatibility_score_calculation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create profiles with matching interests
        $user1->profile()->create([
            'interests' => ['music', 'travel', 'sports'],
            'personality_traits' => ['outgoing', 'adventurous']
        ]);

        $user2->profile()->create([
            'interests' => ['music', 'travel', 'books'],
            'personality_traits' => ['outgoing', 'creative']
        ]);

        $this->actingAs($user1);

        $response = $this->post("/matching/like/{$user2->id}");

        $response->assertStatus(200);
        
        $match = UserMatch::where('user1_id', $user1->id)
            ->where('user2_id', $user2->id)
            ->first();

        $this->assertNotNull($match->compatibility_score);
        $this->assertGreaterThan(0, $match->compatibility_score);
    }

    /**
     * Test matching with age preferences
     */
    public function test_matching_with_age_preferences()
    {
        $user1 = User::factory()->create(['birth_date' => '1990-01-01']);
        $user2 = User::factory()->create(['birth_date' => '1995-01-01']);

        // Set age preferences
        $user1->matchingPreferences()->create([
            'min_age' => 25,
            'max_age' => 35
        ]);

        $this->actingAs($user1);

        $response = $this->get('/discover');

        $response->assertStatus(200);
        $response->assertSee($user2->first_name);
    }

    /**
     * Test matching with gender preferences
     */
    public function test_matching_with_gender_preferences()
    {
        $user1 = User::factory()->create(['gender' => 'male']);
        $user2 = User::factory()->create(['gender' => 'female']);
        $user3 = User::factory()->create(['gender' => 'male']);

        // Set gender preferences
        $user1->matchingPreferences()->create([
            'preferred_genders' => ['female']
        ]);

        $this->actingAs($user1);

        $response = $this->get('/discover');

        $response->assertStatus(200);
        $response->assertSee($user2->first_name);
        $response->assertDontSee($user3->first_name);
    }

    /**
     * Test mutual match creation
     */
    public function test_mutual_match_creation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // User1 likes User2
        $this->actingAs($user1);
        $this->post("/matching/like/{$user2->id}");

        // User2 likes User1 back
        $this->actingAs($user2);
        $response = $this->post("/matching/like/{$user1->id}");

        $response->assertStatus(200);
        
        // Check that both matches are created
        $this->assertDatabaseHas('user_matches', [
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted'
        ]);
    }

    /**
     * Test discover page shows potential matches
     */
    public function test_discover_page_shows_potential_matches()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $this->actingAs($user1);

        $response = $this->get('/discover');

        $response->assertStatus(200);
        $response->assertSee('Descobrir Pessoas');
    }

    /**
     * Test matches page shows user matches
     */
    public function test_matches_page_shows_user_matches()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a match
        UserMatch::create([
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'status' => 'accepted',
            'compatibility_score' => 85.5
        ]);

        $this->actingAs($user1);

        $response = $this->get('/matches');

        $response->assertStatus(200);
        $response->assertSee($user2->first_name);
    }

    /**
     * Test super like limits for free users
     */
    public function test_super_like_limits_for_free_users()
    {
        $user1 = User::factory()->create(['subscription_type' => 'free']);
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $this->actingAs($user1);

        // First super like should work
        $response1 = $this->post("/matching/super-like/{$user2->id}");
        $response1->assertStatus(200);

        // Second super like should fail (free users get 1 per day)
        $response2 = $this->post("/matching/super-like/{$user3->id}");
        $response2->assertStatus(400);
    }

    /**
     * Test super like limits for premium users
     */
    public function test_super_like_limits_for_premium_users()
    {
        $user1 = User::factory()->create(['subscription_type' => 'premium']);
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();

        $this->actingAs($user1);

        // Premium users should be able to super like multiple times
        $response1 = $this->post("/matching/super-like/{$user2->id}");
        $response1->assertStatus(200);

        $response2 = $this->post("/matching/super-like/{$user3->id}");
        $response2->assertStatus(200);

        $response3 = $this->post("/matching/super-like/{$user4->id}");
        $response3->assertStatus(200);
    }
}