<?php


namespace pup\customenchants\types\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\enchantment\MeleeWeaponEnchantment;
use pocketmine\player\Player;
use pup\customenchants\types\ChanceTriat;

class DazeEnchant extends MeleeWeaponEnchantment
{
    use ChanceTriat;
    /**
     * @inheritDoc
     */
    public function isApplicableTo(Entity $victim): bool
    {
        return $victim instanceof Living;
    }

    /**
     * @inheritDoc
     */
    public function getDamageBonus(int $enchantmentLevel): float
    {
        return 0;
    }

    public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel): void
    {
        if ($victim instanceof Player && $attacker instanceof Player) {
            $chance = $this->calculateChance($enchantmentLevel, $this->getMaxLevel(), 5);
            if(random_int(1, 100) <= $chance) {
                $victim->getEffects()->add(new EffectInstance(VanillaEffects::NAUSEA(), $enchantmentLevel * 75, $enchantmentLevel - 1));
            }
        }
    }
}