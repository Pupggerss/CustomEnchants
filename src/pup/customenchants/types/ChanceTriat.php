<?php


namespace pup\customenchants\types;


trait ChanceTriat
{
    public function calculateChance($currentLevel, $maxLevel, $baseChance)
    {
        $currentLevel = max(1, min($currentLevel, $maxLevel));

        if ($currentLevel === $maxLevel) {
            return 100;
        }
        $chanceIncreasePerLevel = (100 - $baseChance) / ($maxLevel - 1);
        $adjustedChance = $baseChance + ($currentLevel - 1) * $chanceIncreasePerLevel;

        return min(100, $adjustedChance);
    }
}