<?php
namespace App\Services;

class LevelService {
    private const XP_PER_LEVEL = 100;

    public static function calculateLevel(int $totalXp): int {
        return (int) floor($totalXp / self::XP_PER_LEVEL) + 1;
    }

    public static function calculateCurrentProgress(int $totalXp): int {
        return $totalXp % self::XP_PER_LEVEL;
    }

    public static function getXpToNextLevel(int $totalXp): int {
        $progress = self::calculateCurrentProgress($totalXp);
        return self::XP_PER_LEVEL - $progress;
    }
}