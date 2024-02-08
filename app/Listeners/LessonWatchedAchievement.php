<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use App\Events\AchievementUnlocked;

class LessonWatchedAchievement
{
    /**
     * Handle the event.
     */
    public function handle(LessonWatched $event): void
    {
        $lessonsWatched = count($event->user->watched);
        $lessonAchievements = getLessonAchievements();
        if (isset($lessonAchievements[$lessonsWatched])) {
            AchievementUnlocked::dispatch([
                'achievement_name' => $lessonAchievements[$lessonsWatched],
                'user' => $event->user,
            ]);
        }
    }
}
