<?php

    namespace Tests\Feature;

    use App\Events\AchievementUnlocked;
    use Illuminate\Support\Facades\Event;
    use Tests\TestCase;
    use App\Models\User;

    class AchievementUnlockedTest extends TestCase {
        /**
         * Test when an achievement is unlocked
         */

        public function test_dispatches_an_event_when_an_achievement_is_unlocked(): void {
            Event::fake([AchievementUnlocked::class]);

            $user            = User::factory()->create();
            $achievementName = 'First Comment Written';
            $badge           = ['achievement_name' => $achievementName, 'user' => $user];

            AchievementUnlocked::dispatch($badge);

            Event::assertDispatched(AchievementUnlocked::class, function($event) use ($user, $achievementName) {
                return $event->achievement['user']->id === $user->id &&
                       $event->achievement['achievement_name'] === $achievementName;
            });
        }
    }
