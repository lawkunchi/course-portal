<?php

    namespace App\Listeners;

    use App\Events\CommentWritten;
    use App\Models\Comment;
    use App\Models\User;
    use App\Events\AchievementUnlocked;

    class CommentWrittenAchievement {

        /**
         * Handle the event.
         */
        public function handle(CommentWritten $event): void {
            //
            $user                = User::findOrFail($event->comment->user_id);
            $commentsWritten     = count($user->comments);
            $commentAchievements = getCommentAchievements(); //
            if (isset($commentAchievements[$commentsWritten])) {
                AchievementUnlocked::dispatch([
                    'achievement_name' => $commentAchievements[$commentsWritten],
                    'user'             => $user,
                ]);
            }
        }
    }
