<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Events\CommentWritten;
use App\Listeners\CommentWrittenAchievement;
use App\Models\Comment;
use App\Models\User;

class CommentWrittenAchievementTest extends TestCase
{

    public function test_dispatches_an_achivement_unlocked_event_when_a_comment_is_written(): void
    {
        Event::fake([AchievementUnlocked::class]);

        // Check that the AchievementUnlocked event is not dispatched before the CommentWrittenAchievement listener handles the CommentWritten event
        Event::assertNotDispatched(AchievementUnlocked::class);
        $user = User::factory()->create();
        $comment = Comment::factory()->for($user)->create();
        $event = new CommentWritten($comment);
        $listener = new CommentWrittenAchievement();
        $listener->handle($event);

        // Check that the AchievementUnlocked event is dispatched with the correct comment
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($user) {
            return $event->achievement['user']->id === $user->id &&
                $event->achievement['achievement_name'] === 'First Comment Written';
        });
    }
}
