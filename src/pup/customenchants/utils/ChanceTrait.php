<?php


namespace pup\customenchants;


trait ChanceTriat
{
    private float $baseChance = 0.1;

    public function setBaseChance(float $chance): void {
        $this->baseChance = max(0, min(1, $chance));
    }

    public function getChance(int $currentLevel, int $maxLevel): float {
        return $this->calculateChance($currentLevel, $maxLevel, $this->baseChance);
    }

    private function calculateChance($currentLevel, $maxLevel, $baseChance)
    {
        $currentLevel = max(1, min($currentLevel, $maxLevel));

        if ($currentLevel === $maxLevel) {
            return 100;
        }
        $chanceIncreasePerLevel = (100 - $baseChance) / ($maxLevel - 1);
        $adjustedChance = $baseChance + ($currentLevel - 1) * $chanceIncreasePerLevel;

        return min(100, $adjustedChance);
    }

    public function hasChanceTrait(): bool{
        return true;
    }
}