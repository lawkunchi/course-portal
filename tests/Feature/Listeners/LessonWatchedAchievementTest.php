<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Listeners\CommentWrittenAchievement;
use App\Listeners\LessonWatchedAchievement;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;

class LessonWatchedAchievementTest extends TestCase
{

    public function test_dispatches_an_achivement_unlocked_event_when_a_lesson_watched(): void
    {
        Event::fake([AchievementUnlocked::class]);

        /* Check that the AchievementUnlocked event is not dispatched before the 
        LessonWatchedAchievement listener handles the LessonWatched event
        */
        Event::assertNotDispatched(AchievementUnlocked::class);

        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(1)->create();
        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);
        $existingLesson = Lesson::first();
        $event = new LessonWatched($existingLesson, $user);
        $listener = new LessonWatchedAchievement();
        $listener->handle($event);

        // Check that the AchievementUnlocked event is dispatched with the correct lesson and user
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($existingLesson, $user) {
            return $event->achievement['user']->id === $user->id && $event->achievement['achievement_name'] === 'First Lesson Watched';
        });
    }
}
