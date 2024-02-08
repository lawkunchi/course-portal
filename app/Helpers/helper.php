<?php


    /**
     * Get badges
     */

    function getBadges() {
        return [
            0  => 'Beginner',
            4  => 'Intermediate',
            8  => 'Advanced',
            10 => 'Master',
        ];
    }

    /**
     * Get Comments Written Achievements
     */

    function getCommentAchievements() {
        return [
            1  => 'First Comment Written',
            3  => '3 Comments Written',
            4  => '5 Comments Written',
            10 => '10 Comments Written',
            20 => '20 Comments Written',
        ];
    }

    /**
     * Get Lessons Watched Achievements
     */

    function getLessonAchievements() {
        return [
            1  => 'First Lesson Watched',
            5  => '5 Lessons Watched',
            10 => '10 Lessons Watched',
            25 => '25 Lessons Watched',
            50 => '50 Lessons Watched',
        ];
    }

     /**
     * Handle earned and not earned achievements
     * @param array $achievements
     * @param int $achievementsCount
     * 
     * @return array
     */

     function handleAchievements($achievements, $achievementsCount): array
    {
        $notAchieved = [];
        foreach ($achievements as $key => $value) {
            if ($key > $achievementsCount) {
                $notAchieved[$key] = $value;
                unset($achievements[$key]);
            }
        }
        return [$achievements, $notAchieved];
    }