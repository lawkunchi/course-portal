<?php

namespace App\Http\Controllers;

use App\Models\User;

class AchievementsController extends Controller
{

    private function handleAchievements($achievements, $achievementsCount)
    {
        $notAchieved = [];
        foreach ($achievements as $key => $value) {
            if ($key > $achievementsCount) {
                $notAchieved[$key] = $value;
                unset($achievements[$key]);
            }
        }
        return [$achievements, $notAchieved];
    }


    public function index(User $user)
    {
        $badges = getBadges();
        list($lessonsAchieved, $lessonsNotAchieved) = self::handleAchievements(
            getLessonAchievements(),
            count($user->watched)
        );
        list($commentAchieved, $commentNotAchieved) = self::handleAchievements(
            getCommentAchievements(),
            count($user->comments)
        );


        $totalAchievements = count($user->comments) + count($user->watched);
        $currentBadge = '';
        $nextBadge = '';

        foreach ($badges as $key => $value) {
            if ($totalAchievements >= $key) {
                $currentBadge = $value;
                $nextBadge = next($badges);
            }
        }

        $nextBadgeAchivements = 0;
        if (isset(array_flip($badges)[$nextBadge])) {
            $nextBadgeAchivements = array_flip($badges)[$nextBadge] - $totalAchievements;
        }

        return response()->json([
            'unlocked_achievements' => [...$commentAchieved, ...$lessonsAchieved],
            'next_available_achievements' => [
                ...array_slice($commentNotAchieved, 0, 1),
                ...array_slice($lessonsNotAchieved, 0, 1)
            ],
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge,
            'remaining_to_unlock_next_badge' => $nextBadgeAchivements
        ]);
    }
}
