<?php


namespace pup\customenchants\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\player\Player;
use pup\customenchants\types\WeaponEnchant;
use Random\RandomException;

class DazeEnchant extends WeaponEnchant
{

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

    /**
     * @throws RandomException
     */
    public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel)
    : void
    {
        if ($victim instanceof Player && $attacker instanceof Player) {
            $chance = $this->getChance($enchantmentLevel, $this->getMaxLevel());
            if (random_int(1, 100) <= $chance) {
                $victim->getEffects()->add(new EffectInstance(VanillaEffects::NAUSEA(), $enchantmentLevel * 75, min($enchantmentLevel - 1, 1)));
            }
        }
    }
}