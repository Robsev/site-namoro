<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMatch;
use App\Models\UserProfile;
use App\Models\MatchingPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MatchingController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas, não no controller
    }

    /**
     * Show the discovery page with potential matches
     */
    public function discover()
    {
        $user = Auth::user();
        $user->load(['profile', 'matchingPreferences', 'photos']);
        
        // Get potential matches
        $potentialMatches = $this->getPotentialMatches($user);
        
        return view('matching.discover', compact('potentialMatches'));
    }

    /**
     * Load more potential matches via AJAX
     */
    public function loadMore(Request $request)
    {
        $user = Auth::user();
        $user->load(['profile', 'matchingPreferences', 'photos']);
        
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        
        // Get potential matches with offset
        $potentialMatches = $this->getPotentialMatches($user, $offset, $limit);
        
        if ($potentialMatches->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Não há mais pessoas para mostrar',
                'hasMore' => false
            ]);
        }
        
        // Render the matches as HTML
        $html = view('matching.partials.match-cards', compact('potentialMatches'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'hasMore' => $potentialMatches->count() >= $limit,
            'count' => $potentialMatches->count()
        ]);
    }

    /**
     * Show all matches for the current user
     */
    public function matches()
    {
        $user = Auth::user();
        
        $matches = UserMatch::where(function($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
        })
        ->where('status', 'accepted')
        ->with(['user1', 'user2'])
        ->get()
        ->map(function($match) use ($user) {
            $match->other_user = $match->getOtherUser($user->id);
            return $match;
        });

        return view('matching.matches', compact('matches'));
    }

    /**
     * Show likes sent by the current user
     */
    public function likesSent()
    {
        $user = Auth::user();
        
        $likesSent = UserMatch::where('user1_id', $user->id)
            ->where('status', 'pending')
            ->with(['user2.profile', 'user2.photos' => function($query) {
                $query->where('is_approved', true)->orderBy('sort_order');
            }])
            ->orderBy('matched_at', 'desc')
            ->paginate(12);

        return view('matching.likes-sent', compact('likesSent'));
    }

    /**
     * Show likes received by the current user
     */
    public function likesReceived()
    {
        $user = Auth::user();
        
        $likesReceived = UserMatch::where('user2_id', $user->id)
            ->where('status', 'pending')
            ->with(['user1.profile', 'user1.photos' => function($query) {
                $query->where('is_approved', true)->orderBy('sort_order');
            }])
            ->orderBy('matched_at', 'desc')
            ->paginate(12);

        return view('matching.likes-received', compact('likesReceived'));
    }

    /**
     * Like a user (create or update match)
     */
    public function like(Request $request, $userId)
    {
        $user = Auth::user();
        
        // Manually find the target user
        $targetUser = User::find($userId);
        
        // Debug: Log the target user
        \Log::info('Like request', [
            'user_id' => $user->id,
            'target_user_id' => $targetUser ? $targetUser->id : 'NULL',
            'target_user' => $targetUser,
            'request_route' => $request->route()->getName(),
            'request_url' => $request->url(),
            'route_parameters' => $request->route()->parameters()
        ]);
        
        // Check if targetUser is valid
        if (!$targetUser) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }
        
        // Prevent self-liking
        if ($user->id === $targetUser->id) {
            return response()->json(['error' => 'Você não pode curtir a si mesmo'], 400);
        }

        // Check if match already exists
        $existingMatch = UserMatch::where(function($query) use ($user, $targetUser) {
            $query->where('user1_id', $user->id)->where('user2_id', $targetUser->id);
        })->orWhere(function($query) use ($user, $targetUser) {
            $query->where('user1_id', $targetUser->id)->where('user2_id', $user->id);
        })->first();

        if ($existingMatch) {
            // Check if this is a mutual like (both users liked each other)
            if ($existingMatch->status === 'pending') {
                // Check if there's a reverse match
                $reverseMatch = UserMatch::where('user1_id', $targetUser->id)
                    ->where('user2_id', $user->id)
                    ->where('status', 'pending')
                    ->first();
                
                if ($reverseMatch) {
                    // Mutual like! Update both matches to accepted
                    $existingMatch->update(['status' => 'accepted']);
                    $reverseMatch->update(['status' => 'accepted']);
                    
                    // Create conversation
                    $conversation = \App\Models\Conversation::create([
                        'user1_id' => $user->id,
                        'user2_id' => $targetUser->id,
                        'last_message_at' => now(),
                    ]);
                    
                    // Send match notifications to both users
                    $matchData = [
                        'compatibility_score' => $existingMatch->compatibility_score,
                        'match_reason' => $existingMatch->match_reason,
                        'conversation_id' => $conversation->id,
                    ];
                    
                    $user->notify(new \App\Notifications\NewMatch($targetUser, $matchData));
                    $targetUser->notify(new \App\Notifications\NewMatch($user, $matchData));
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Match criado! Vocês podem conversar agora!',
                        'match' => $existingMatch,
                        'is_mutual' => true
                    ]);
                } else {
                    return response()->json(['error' => 'Match já existe'], 400);
                }
            } else {
                return response()->json(['error' => 'Match já existe'], 400);
            }
        }

        // Calculate compatibility score
        $compatibilityScore = $this->calculateCompatibility($user, $targetUser);
        
        // Create new match
        $match = UserMatch::create([
            'user1_id' => $user->id,
            'user2_id' => $targetUser->id,
            'compatibility_score' => $compatibilityScore,
            'status' => 'pending',
            'matched_at' => now(),
            'match_reason' => $this->generateMatchReason($user, $targetUser, $compatibilityScore)
        ]);

        // Send notification to the liked user
        $targetUser->notify(new \App\Notifications\NewLike($user));

        return response()->json([
            'success' => true,
            'message' => 'Curtida enviada!',
            'match' => $match
        ]);
    }

    /**
     * Undo a like (remove match)
     */
    public function undoLike(Request $request, $userId)
    {
        $user = Auth::user();
        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        // Find and delete the match
        $match = UserMatch::where('user1_id', $user->id)
            ->where('user2_id', $targetUser->id)
            ->where('status', 'pending')
            ->first();

        if (!$match) {
            return response()->json(['error' => 'Like não encontrado'], 404);
        }

        $match->delete();

        return response()->json([
            'success' => true,
            'message' => 'Like removido com sucesso!'
        ]);
    }

    /**
     * Pass on a user (reject)
     */
    public function pass(Request $request, $userId)
    {
        $user = Auth::user();
        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }
        
        // Create a rejected match to avoid showing this user again
        UserMatch::create([
            'user1_id' => $user->id,
            'user2_id' => $targetUser->id,
            'compatibility_score' => 0,
            'status' => 'rejected',
            'matched_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuário passado'
        ]);
    }

    /**
     * Super like a user
     */
    public function superLike(Request $request, $userId)
    {
        $user = Auth::user();
        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }
        
        // Check if user has super likes available
        if (!$this->hasSuperLikeAvailable($user)) {
            return response()->json(['error' => 'Você não tem super likes disponíveis'], 400);
        }

        // Create super like match
        $match = UserMatch::create([
            'user1_id' => $user->id,
            'user2_id' => $targetUser->id,
            'compatibility_score' => $this->calculateCompatibility($user, $targetUser),
            'status' => 'pending',
            'matched_at' => now(),
            'is_super_like' => true,
            'match_reason' => 'Super Like!'
        ]);

        // Send notification to the super liked user
        $targetUser->notify(new \App\Notifications\NewSuperLike($user));

        return response()->json([
            'success' => true,
            'message' => 'Super Like enviado!',
            'match' => $match
        ]);
    }

    /**
     * Get potential matches for a user
     */
    private function getPotentialMatches(User $user, $offset = 0, $limit = 20)
    {
        $userPreferences = $user->matchingPreferences;
        
        if (!$userPreferences) {
            return collect();
        }

        // Get users that haven't been matched with yet
        $excludedUserIds = UserMatch::where(function($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
        })->get()
        ->map(function($match) use ($user) {
            return $match->user1_id === $user->id ? $match->user2_id : $match->user1_id;
        })
        ->unique()
        ->push($user->id);

        $query = User::whereNotIn('id', $excludedUserIds)
            ->with(['profile', 'photos' => function($query) {
                $query->where('is_approved', true)->orderBy('sort_order');
            }]);

        // Apply gender preference
        if ($userPreferences->preferred_genders) {
            $query->whereIn('gender', $userPreferences->preferred_genders);
        }

        // Apply age range
        if ($userPreferences->min_age || $userPreferences->max_age) {
            $query->whereHas('profile', function($q) use ($userPreferences) {
                if ($userPreferences->min_age) {
                    $q->whereRaw('YEAR(CURDATE()) - YEAR(birth_date) >= ?', [$userPreferences->min_age]);
                }
                if ($userPreferences->max_age) {
                    $q->whereRaw('YEAR(CURDATE()) - YEAR(birth_date) <= ?', [$userPreferences->max_age]);
                }
            });
        }

        // Apply distance filter using geolocation (only if geographic matching is enabled)
        if ($userPreferences->enable_geographic_matching && $userPreferences->max_distance && $user->hasGeolocation()) {
            $maxDistance = $userPreferences->max_distance;
            
            // Use Haversine formula to filter by distance
            $query->whereNotNull('latitude')
                  ->whereNotNull('longitude')
                  ->whereRaw("
                      (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                      cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                      sin(radians(latitude)))) <= ?
                  ", [$user->latitude, $user->longitude, $user->latitude, $maxDistance]);
        }

        // Apply other filters
        if ($userPreferences->verified_only) {
            $query->where('is_verified', true);
        }

        if ($userPreferences->online_only) {
            $query->where('last_seen', '>=', now()->subHours(24));
        }

        if ($userPreferences->photos_only) {
            $query->whereHas('photos', function($q) {
                $q->where('moderation_status', 'approved');
            });
        }

        if ($userPreferences->complete_profiles_only) {
            // Filter for users with 100% profile completeness
            $query->whereRaw('
                (CASE 
                    WHEN first_name IS NOT NULL AND first_name != "" THEN 5 ELSE 0 END +
                    WHEN last_name IS NOT NULL AND last_name != "" THEN 5 ELSE 0 END +
                    WHEN birth_date IS NOT NULL THEN 5 ELSE 0 END +
                    WHEN gender IS NOT NULL AND gender != "" THEN 5 ELSE 0 END +
                    WHEN location IS NOT NULL AND location != "" THEN 5 ELSE 0 END +
                    WHEN profile_photo IS NOT NULL AND profile_photo != "" THEN 5 ELSE 0 END +
                    (SELECT CASE WHEN bio IS NOT NULL AND bio != "" THEN 10 ELSE 0 END FROM profiles WHERE profiles.user_id = users.id) +
                    (SELECT CASE WHEN relationship_goal IS NOT NULL AND relationship_goal != "" THEN 5 ELSE 0 END FROM profiles WHERE profiles.user_id = users.id) +
                    (SELECT CASE WHEN education_level IS NOT NULL AND education_level != "" THEN 5 ELSE 0 END FROM profiles WHERE profiles.user_id = users.id) +
                    (SELECT CASE WHEN smoking IS NOT NULL AND smoking != "" THEN 2.5 ELSE 0 END FROM profiles WHERE profiles.user_id = users.id) +
                    (SELECT CASE WHEN drinking IS NOT NULL AND drinking != "" THEN 2.5 ELSE 0 END FROM profiles WHERE profiles.user_id = users.id) +
                    (SELECT CASE WHEN COUNT(*) >= 1 THEN 10 ELSE 0 END FROM user_photos WHERE user_photos.user_id = users.id AND moderation_status = "approved") +
                    (SELECT CASE WHEN COUNT(*) >= 2 THEN 5 ELSE 0 END FROM user_photos WHERE user_photos.user_id = users.id AND moderation_status = "approved") +
                    (SELECT CASE WHEN COUNT(*) >= 3 THEN 5 ELSE 0 END FROM user_photos WHERE user_photos.user_id = users.id AND moderation_status = "approved") +
                    (SELECT CASE WHEN COUNT(*) >= 3 THEN 10 ELSE 0 END FROM user_interests WHERE user_interests.user_id = users.id) +
                    (SELECT CASE WHEN COUNT(*) >= 5 THEN 5 ELSE 0 END FROM user_interests WHERE user_interests.user_id = users.id) +
                    (SELECT CASE WHEN COUNT(*) > 0 THEN 10 ELSE 0 END FROM psychological_profiles WHERE psychological_profiles.user_id = users.id)
                ) / 100 * 100 = 100
            ');
        }

        return $query->offset($offset)->limit($limit)->get()->map(function($match) use ($user) {
            $match->compatibility_score = $this->calculateCompatibility($user, $match);
            $match->distance = $match->distanceFrom($user->latitude, $user->longitude);
            $match->profile_completeness = $match->profile_completeness;
            return $match;
        })->sortBy(function($match) {
            // Sort by distance first (if available), then by compatibility
            return [$match->distance ?? 999, -$match->compatibility_score];
        });
    }

    /**
     * Calculate compatibility score between two users
     */
    private function calculateCompatibility(User $user1, User $user2)
    {
        $score = 0;
        $maxScore = 100;

        // Basic compatibility (20 points)
        if ($user1->profile && $user2->profile) {
            // Age compatibility (10 points)
            $ageScore = $this->calculateAgeCompatibility($user1, $user2);
            $score += $ageScore;

            // Interest compatibility (10 points)
            $interestScore = $this->calculateInterestCompatibility($user1, $user2);
            $score += $interestScore;
        }

        // Personality compatibility (30 points)
        $personalityScore = $this->calculatePersonalityCompatibility($user1, $user2);
        $score += $personalityScore;

        // Lifestyle compatibility (20 points)
        $lifestyleScore = $this->calculateLifestyleCompatibility($user1, $user2);
        $score += $lifestyleScore;

        // Relationship goals compatibility (20 points)
        $relationshipScore = $this->calculateRelationshipCompatibility($user1, $user2);
        $score += $relationshipScore;

        // Education compatibility (10 points)
        $educationScore = $this->calculateEducationCompatibility($user1, $user2);
        $score += $educationScore;

        return min($score, $maxScore);
    }

    /**
     * Calculate age compatibility
     */
    private function calculateAgeCompatibility(User $user1, User $user2)
    {
        if (!$user1->birth_date || !$user2->birth_date) {
            return 5; // Neutral score if no age data
        }

        $age1 = $user1->age;
        $age2 = $user2->age;
        $ageDiff = abs($age1 - $age2);

        if ($ageDiff <= 2) return 10;
        if ($ageDiff <= 5) return 8;
        if ($ageDiff <= 10) return 6;
        if ($ageDiff <= 15) return 4;
        return 2;
    }

    /**
     * Calculate interest compatibility
     */
    private function calculateInterestCompatibility(User $user1, User $user2)
    {
        // Use the new interests relationship instead of profile
        $interests1 = $user1->interests()->pluck('interest_value')->toArray();
        $interests2 = $user2->interests()->pluck('interest_value')->toArray();

        if (empty($interests1) || empty($interests2)) {
            return 5; // Neutral score if no interest data
        }

        $commonInterests = array_intersect($interests1, $interests2);
        $totalInterests = count(array_unique(array_merge($interests1, $interests2)));

        if ($totalInterests === 0) return 5;

        $compatibility = (count($commonInterests) / $totalInterests) * 10;
        return round($compatibility);
    }

    /**
     * Calculate personality compatibility
     */
    private function calculatePersonalityCompatibility(User $user1, User $user2)
    {
        $profile1 = $user1->psychologicalProfile;
        $profile2 = $user2->psychologicalProfile;

        if (!$profile1 || !$profile2) {
            return 15; // Neutral score if no personality data
        }

        // Calculate Big Five compatibility
        $bigFiveScore = 0;
        $bigFiveTraits = ['openness', 'conscientiousness', 'extraversion', 'agreeableness', 'neuroticism'];
        
        foreach ($bigFiveTraits as $trait) {
            $score1 = $profile1->$trait ?? 0;
            $score2 = $profile2->$trait ?? 0;
            $diff = abs($score1 - $score2);
            
            // Lower difference = higher compatibility
            $bigFiveScore += max(0, 6 - $diff);
        }

        return min(30, $bigFiveScore);
    }

    /**
     * Calculate lifestyle compatibility
     */
    private function calculateLifestyleCompatibility(User $user1, User $user2)
    {
        $score = 0;

        // Check if profiles exist
        if (!$user1->profile || !$user2->profile) {
            return 10; // Neutral score if no profile data
        }

        // Smoking compatibility
        if ($user1->profile->smoking === $user2->profile->smoking) {
            $score += 5;
        } elseif (($user1->profile->smoking === 'never' && $user2->profile->smoking === 'regularly') ||
                  ($user2->profile->smoking === 'never' && $user1->profile->smoking === 'regularly')) {
            $score -= 5;
        }

        // Drinking compatibility
        if ($user1->profile->drinking === $user2->profile->drinking) {
            $score += 5;
        }

        // Exercise compatibility (removed - not essential for matching)

        // Education level compatibility
        if ($user1->profile->education_level === $user2->profile->education_level) {
            $score += 5;
        }

        return max(0, min(20, $score + 10)); // Normalize to 0-20 range
    }

    /**
     * Calculate relationship goals compatibility
     */
    private function calculateRelationshipCompatibility(User $user1, User $user2)
    {
        // Check if profiles exist
        if (!$user1->profile || !$user2->profile) {
            return 10; // Neutral score if no profile data
        }

        $goal1 = $user1->profile->relationship_goal;
        $goal2 = $user2->profile->relationship_goal;

        if (!$goal1 || !$goal2) {
            return 10; // Neutral score if no goal data
        }

        if ($goal1 === $goal2) {
            return 20; // Perfect match
        }

        // Compatible goals
        $compatiblePairs = [
            ['friendship', 'casual'],
            ['romance', 'serious'],
            ['serious', 'marriage'],
            ['casual', 'romance']
        ];

        foreach ($compatiblePairs as $pair) {
            if (($goal1 === $pair[0] && $goal2 === $pair[1]) ||
                ($goal1 === $pair[1] && $goal2 === $pair[0])) {
                return 15;
            }
        }

        return 5; // Low compatibility
    }

    /**
     * Calculate education compatibility
     */
    private function calculateEducationCompatibility(User $user1, User $user2)
    {
        // Check if profiles exist
        if (!$user1->profile || !$user2->profile) {
            return 5; // Neutral score if no profile data
        }

        $edu1 = $user1->profile->education_level;
        $edu2 = $user2->profile->education_level;

        if (!$edu1 || !$edu2) {
            return 5; // Neutral score if no education data
        }

        if ($edu1 === $edu2) {
            return 10; // Perfect match
        }

        // Similar education levels
        $educationLevels = ['high_school', 'bachelor', 'master', 'phd'];
        $index1 = array_search($edu1, $educationLevels);
        $index2 = array_search($edu2, $educationLevels);

        if ($index1 !== false && $index2 !== false) {
            $diff = abs($index1 - $index2);
            if ($diff === 1) return 8;
            if ($diff === 2) return 6;
            if ($diff === 3) return 4;
        }

        return 5; // Low compatibility
    }

    /**
     * Generate match reason based on compatibility
     */
    private function generateMatchReason(User $user1, User $user2, $score)
    {
        $reasons = [];

        // Interest-based reasons
        $user1Interests = $user1->interests()->pluck('interest_value')->toArray();
        $user2Interests = $user2->interests()->pluck('interest_value')->toArray();
        $commonInterests = array_intersect($user1Interests, $user2Interests);

        if (!empty($commonInterests)) {
            $reasons[] = __("messages.matching.shared_interests") . ": " . implode(', ', array_slice($commonInterests, 0, 3));
        }

        // Personality-based reasons
        $profile1 = $user1->psychologicalProfile;
        $profile2 = $user2->psychologicalProfile;
        
        if ($profile1 && $profile2) {
            $reasons[] = __("messages.matching.psychological_compatible");
        }

        // Age-based reasons
        if ($user1->age && $user2->age) {
            $ageDiff = abs($user1->age - $user2->age);
            if ($ageDiff <= 3) {
                $reasons[] = __("messages.matching.compatible_ages");
            }
        }

        // High compatibility
        if ($score >= 80) {
            $reasons[] = __("messages.matching.high_compatibility");
        } elseif ($score >= 60) {
            $reasons[] = __("messages.matching.good_compatibility");
        }

        return !empty($reasons) ? implode('. ', $reasons) : __("messages.matching.good_match");
    }

    /**
     * Check if user has super like available
     */
    private function hasSuperLikeAvailable(User $user)
    {
        // Free users get 1 super like per day
        // Premium users get 5 super likes per day
        $dailyLimit = $user->subscription_type === 'premium' ? 5 : 1;
        
        $todaySuperLikes = UserMatch::where('user1_id', $user->id)
            ->where('is_super_like', true)
            ->whereDate('created_at', today())
            ->count();

        return $todaySuperLikes < $dailyLimit;
    }
    
    /**
     * Accept a like received (create match)
     */
    public function acceptLike(Request $request, $userId)
    {
        $user = Auth::user();
        
        // Find the pending match where user2 is the current user and user1 is the target
        $match = UserMatch::where('user1_id', $userId)
            ->where('user2_id', $user->id)
            ->where('status', 'pending')
            ->first();
        
        if (!$match) {
            return response()->json(['error' => 'Like não encontrado'], 404);
        }
        
        // Update status to accepted
        $match->update([
            'status' => 'accepted',
            'responded_at' => now()
        ]);
        
        // Send notification to the user who sent the like
        $liker = User::find($userId);
        if ($liker) {
            $liker->notify(new \App\Notifications\MatchAccepted($user));
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Match criado! Vocês podem conversar agora!'
        ]);
    }
}
