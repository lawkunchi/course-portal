<?php

namespace Tests\Feature;

use App\Events\BadgeUnclocked;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Comment;
use App\Events\CommentWritten;
use App\Events\LessonWatched;

class BadgeUnlockedTest extends TestCase
{
    /**
     * Test when an event badge is unlocked
     */

    public function test_dispatches_an_event_when_a_badge_is_unlocked(): void
    {
        Event::fake([BadgeUnclocked::class]);

        $user      = User::factory()->create();
        $badgeName = 'Beginner';
        $badge     = ['badge_name' => $badgeName, 'user' => $user];

        // Check that the BadgeUnlocked event is not dispatched before the user actions
        Event::assertNotDispatched(BadgeUnclocked::class);

        BadgeUnclocked::dispatch($badge);

        // Check that the BadgeUnlocked event is dispatched with the correct user and badge name
        Event::assertDispatched(BadgeUnclocked::class, function ($event) use ($user, $badgeName) {
            return $event->badge['user']->id === $user->id && $event->badge['badge_name'] === $badgeName;
        });
    }


    /**
     * Test when beginner badge is unclocked
     */

    public function test_check_beginner_badge_is_unlocked(): void
    {

        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200);

        $response->assertJson([
            'unlocked_achievements'          => [],
            'next_available_achievements'    => [
                'First Comment Written',
                'First Lesson Watched',
            ],
            'current_badge'                  => 'Beginner',
            'next_badge'                     => 'Intermediate',
            'remaining_to_unlock_next_badge' => 4,
        ]);
    }


    /**
     * Test when beginner intermediate is unclocked
     */

    public function test_check_intermediate_badge_is_unlocked(): void
    {

        $user    = User::factory()->create();
        $lessons = Lesson::factory()->count(5)->create();
        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);
        Comment::factory()->count(3)->for($user)->create();
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200);

        $response->assertJson([
            'unlocked_achievements'          => [
                'First Comment Written',
                '3 Comments Written',
                'First Lesson Watched',
                '5 Lessons Watched',
            ],
            'next_available_achievements'    => [
                '5 Comments Written',
                '10 Lessons Watched',
            ],
            'current_badge'                  => 'Intermediate',
            'next_badge'                     => 'Advanced',
            'remaining_to_unlock_next_badge' => 4,
        ]);
    }

    /**
     * Test when advanced intermediate is unclocked
     */
    public function test_check_advanced_badge_is_unlocked(): void
    {

        $user    = User::factory()->create();
        $lessons = Lesson::factory()->count(10)->create();
        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);
        Comment::factory()->count(20)->for($user)->create();
        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200);

        $response->assertJson([
            'unlocked_achievements'          => [
                'First Comment Written',
                '3 Comments Written',
                '5 Comments Written',
                '10 Comments Written',
                '20 Comments Written',
                'First Lesson Watched',
                '5 Lessons Watched',
                '10 Lessons Watched',
            ],
            'next_available_achievements'    => [
                '25 Lessons Watched',
            ],
            'current_badge'                  => 'Advanced',
            'next_badge'                     => 'Master',
            'remaining_to_unlock_next_badge' => 2,
        ]);
    }

    /**
     * Test when master intermediate is unclocked
     */
    public function test_check_master_badge_is_unlocked(): void
    {

        $user    = User::factory()->create();
        $lessons = Lesson::factory()->count(50)->create();

        $user->lessons()->attach($lessons->pluck('id'), ['watched' => true]);
        Comment::factory()->count(20)->for($user)->create();

        $response = $this->get("/users/{$user->id}/achievements");
        $response->assertStatus(200);

        $response->assertJson([
            'unlocked_achievements'          => [
                'First Comment Written',
                '3 Comments Written',
                '5 Comments Written',
                '10 Comments Written',
                '20 Comments Written',
                'First Lesson Watched',
                '5 Lessons Watched',
                '10 Lessons Watched',
                '25 Lessons Watched',
                '50 Lessons Watched',
            ],
            'next_available_achievements'    => [],
            'current_badge'                  => 'Master',
            'next_badge'                     => '',
            'remaining_to_unlock_next_badge' => 0,
        ]);
    }
}
