<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Events\CommentWritten;
use App\Listeners\CommentWrittenAchievement;
use App\Models\Comment;
use App\Models\User;

class CommentWrittenTest extends TestCase
{
    /**
     * A basic test example.
     */


    public function test_dispatches_an_event_when_a_comment_is_written(): void
    {
        Event::fake([CommentWritten::class]);

        // Check that the CommentWritten event is not dispatched before the comment is created
        Event::assertNotDispatched(CommentWritten::class);

        $comment = Comment::factory()->create();
        CommentWritten::dispatch($comment);

        // Check that the CommentWritten event is dispatched with the correct comment
        Event::assertDispatched(CommentWritten::class, function ($event) use ($comment) {
            return $event->comment->id === $comment->id;
        });
    }

    public function test_event_listener_when_a_comment_is_written(): void
    {
        Event::fake([CommentWritten::class]);

        // Check that the CommentWritten event is not dispatched before the comment is created
        Event::assertNotDispatched(CommentWritten::class);

        $comment = Comment::factory()->create();
        CommentWritten::dispatch($comment);

        // Check that the CommentWritten event is dispatched with the correct comment
        Event::assertDispatched(CommentWritten::class, function ($event) use ($comment) {
            return $event->comment->id === $comment->id;
        });

        // Check that the CommentWrittenAchievement listener is listening to the CommentWritten event
        Event::assertListening(
            CommentWritten::class,
            CommentWrittenAchievement::class
        );
    }
}
