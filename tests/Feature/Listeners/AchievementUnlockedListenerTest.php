<?php

namespace Tests\Feature;

use App\Events\BadgeUnclocked;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Events\CommentWritten;
use App\Models\Comment;
use App\Models\User;
use App\Events\LessonWatched;
use App\Models\Lesson;
use App\Listeners\AchievementUnlockedListener;

class AchievementUnlockedListenerTest extends TestCase
{
    /**
     * A basic test example.
     */


    public function test_dispatches_a_badge_unlocked_listener_when_a_comment_is_written(): void
    {
        $user = User::factory()->create();
        Comment::factory()->count(4)->for($user)->create();

        Event::fake([BadgeUnclocked::class]);

        // Check that the BadgeUnlocked event is not dispatched before the CommentWritten event
        Event::assertNotDispatched(BadgeUnclocked::class);

        $comment = Comment::latest()->first();
        event(new CommentWritten($comment));

        // Check that the BadgeUnlocked event is dispatched with the correct user and badge name
        Event::assertDispatched(BadgeUnclocked::class, function ($event) use ($user) {
            return $event->badge['user']->id === $user->id && $event->badge['badge_name'] === 'Intermediate';
        });
    }


    public function test_dispatches_a_badge_unlocked_listener_when_a_lesson_is_watched(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(8)->create();
        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);

        Event::fake([BadgeUnclocked::class]);

        // Check that the BadgeUnlocked event is not dispatched before the LessonWatched event
        Event::assertNotDispatched(BadgeUnclocked::class);

        $lesson = Lesson::first();
        event(new LessonWatched($lesson, $user));

        // Check that the BadgeUnlocked event is dispatched with the correct user and badge name
        Event::assertDispatched(BadgeUnclocked::class, function ($event) use ($user) {
            return $event->badge['user']->id === $user->id && $event->badge['badge_name'] === 'Advanced';
        });
    }
}
