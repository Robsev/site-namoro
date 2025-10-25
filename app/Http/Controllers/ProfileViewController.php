<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileViewController extends Controller
{
    /**
     * Display a user's public profile
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Prevent viewing own profile
        if ($currentUser->id === $user->id) {
            return redirect()->route('profile.show');
        }

        // Load user data with relationships
        $user->load([
            'profile',
            'photos' => function($query) {
                $query->where('is_approved', true)->orderBy('sort_order');
            },
            'interests.interestCategory',
            'psychologicalProfile'
        ]);

        // Check if there's an existing match
        $existingMatch = \App\Models\UserMatch::where(function($query) use ($currentUser, $user) {
            $query->where('user1_id', $currentUser->id)->where('user2_id', $user->id);
        })->orWhere(function($query) use ($currentUser, $user) {
            $query->where('user1_id', $user->id)->where('user2_id', $currentUser->id);
        })->first();

        // Calculate compatibility if both users have profiles
        $compatibilityScore = null;
        if ($user->profile && $currentUser->profile) {
            $compatibilityScore = $this->calculateCompatibility($currentUser, $user);
        }

        return view('profile.view', compact('user', 'existingMatch', 'compatibilityScore'));
    }

    /**
     * Calculate compatibility between two users
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

        // Exercise compatibility
        if ($user1->profile->exercise_frequency === $user2->profile->exercise_frequency) {
            $score += 5;
        }

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

        // Define compatible goal pairs
        $compatiblePairs = [
            ['friendship', 'romance'],
            ['romance', 'marriage'],
            ['marriage', 'long_term'],
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

        // Define education hierarchy
        $educationLevels = [
            'high_school' => 1,
            'bachelor' => 2,
            'master' => 3,
            'phd' => 4,
        ];

        $level1 = $educationLevels[$edu1] ?? 0;
        $level2 = $educationLevels[$edu2] ?? 0;
        $diff = abs($level1 - $level2);

        if ($diff === 0) return 10;
        if ($diff === 1) return 8;
        if ($diff === 2) return 6;
        return 4;
    }
}