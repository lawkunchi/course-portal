<?php

namespace App\Listeners;

use App\Events\BadgeUnclocked;
use App\Models\User;

class AchievementUnlockedListener
{

    /**
     * Handle CommentWritten Event
     */
    public function onCommentWritten($event)
    {
        $user = User::findOrFail($event->comment->user_id);

        $badges = getBadges();
        list($lessonsAchieved) = handleAchievements(
            getLessonAchievements(),
            count($user->watched)
        );
        list($commentAchieved) = handleAchievements(
            getCommentAchievements(),
            count($user->comments)
        );

        $achievementArray = [...$commentAchieved, ...$lessonsAchieved];
        $totalAchievements = count($achievementArray);
        // Get the current and next badge
        $badge = null;
        foreach ($badges as $key => $value) {
            if ($totalAchievements >= $key) {
                $badge = $value;
            }
        }

        if ($badge) {
            BadgeUnclocked::dispatch([
                'badge_name' => $badge,
                'user' => $user,
            ]);
        }
    }

    /**
     * Handle LessonWatched Event
     */
    public function onLessonWatched($event)
    {
        $totalAchievements = count($event->user->watched) + count($event->user->comments);
        $badges = getBadges();
        if (isset($badges[$totalAchievements])) {
            BadgeUnclocked::dispatch([
                'badge_name' => $badges[$totalAchievements],
                'user' => $event->user,
            ]);
        }
    }


    public function subscribe($events)
    {
        $events->listen(
            'App\Events\CommentWritten',
            'App\Listeners\AchievementUnlockedListener@onCommentWritten'
        );

        $events->listen(
            'App\Events\LessonWatched',
            'App\Listeners\AchievementUnlockedListener@onLessonWatched'
        );
    }
}
