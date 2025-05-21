<?php


namespace pup\customenchants\types;


use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\MeleeWeaponEnchantment;
use pup\customenchants\utils\ChanceTrait;

abstract class WeaponEnchant extends MeleeWeaponEnchantment
{
    use ChanceTrait;


    //Best way to do this while still using MeleeWeapon class?
    private string $description;

    public function __construct(string $name, int $rarity, string $description, int $maxLevel, int $primaryFlag, int $secondaryFlag = ItemFlags::NONE) {
        $this->description = $description;
        parent::__construct($name, $rarity, $primaryFlag, $secondaryFlag, $maxLevel);
    }

    public function getDescription(): string{
        return $this->description;
    }
    /**
     * Make these abstract instead?
     */

    /**
     * @inheritDoc
     */
    public function isApplicableTo(Entity $victim)
    : bool
    {
        return $victim instanceof Living;
    }

    /**
     * @inheritDoc
     */
    public function getDamageBonus(int $enchantmentLevel)
    : float
    {
        return 0;
    }
}