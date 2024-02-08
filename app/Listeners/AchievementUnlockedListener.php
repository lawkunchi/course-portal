<?php

namespace App\Listeners;

use App\Events\BadgeUnclocked;
use App\Models\User;

class AchievementUnlockedListener
{

    /**
     * Handle user login events.
     */
    public function onCommentWritten($event)
    {
        $user = User::findOrFail($event->comment->user_id);

        $totalAchievements = count($user->watched) + count($user->comments);
        $badges = getBadges();
        if (isset($badges[$totalAchievements])) {
            BadgeUnclocked::dispatch([
                'badge_name' => $badges[$totalAchievements],
                'user' => $user,
            ]);
        }
    }

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
