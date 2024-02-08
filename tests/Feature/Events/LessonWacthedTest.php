<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Events\LessonWatched;
use App\Listeners\LessonWatchedAchievement;
use App\Models\Lesson;
use App\Models\User;

class LessonWacthedTest extends TestCase
{
    /**
     * A basic test example.
     */

    public function test_dispatches_an_event_when_a_lesson_is_watched(): void
    {
        Event::fake([LessonWatched::class]);

        // Check that the LessonWatched event is not dispatched before the lesson is watched
        Event::assertNotDispatched(LessonWatched::class);

        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();
        LessonWatched::dispatch($lesson, $user);

        // Check that the LessonWatched event is dispatched with the correct lesson and user
        Event::assertDispatched(LessonWatched::class, function ($event) use ($lesson, $user) {
            return $event->lesson->id === $lesson->id && $event->user->id === $user->id;
        });
    }

    public function test_event_listener_when_a_lesson_is_watched(): void
    {
        Event::fake([LessonWatched::class]);

        // Check that the LessonWatched event is not dispatched before the lesson is watched
        Event::assertNotDispatched(LessonWatched::class);

        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();
        LessonWatched::dispatch($lesson, $user);

        // Check that the LessonWatched event is dispatched with the correct lesson and user
        Event::assertDispatched(LessonWatched::class, function ($event) use ($lesson, $user) {
            return $event->lesson->id === $lesson->id && $event->user->id === $user->id;
        });

        // Check that the LessonWatchedAchievement listener is listening to the LessonWatched event
        Event::assertListening(
            LessonWatched::class,
            LessonWatchedAchievement::class
        );
    }
}
