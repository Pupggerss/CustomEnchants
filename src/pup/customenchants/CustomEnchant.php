<?php


namespace pup\customenchants;


use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pup\customenchants\utils\ChanceTrait;


abstract class CustomEnchant extends Enchantment
{
    use ChanceTrait;

    private string $description;

    public function __construct(string $name, int $rarity, string $description, int $maxLevel, int $primaryFlag, int $secondaryFlag = ItemFlags::NONE) {
        $this->description = $description;
        parent::__construct($name, $rarity, $primaryFlag, $secondaryFlag, $maxLevel);
    }

    public function getDescription(): string{
        return $this->description;
    }
}