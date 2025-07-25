<?php


namespace pup\customenchants\enchants\sword;


use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\world\particle\FlameParticle;
use pup\customenchants\types\WeaponEnchant;
use Random\RandomException;

class AronistEnchant extends WeaponEnchant
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
                while ($attacker->isOnFire()) {
                    $additionalDamage = $enchantmentLevel * 1.5;

                    $event = new EntityDamageByEntityEvent($attacker, $victim, EntityDamageEvent::CAUSE_CUSTOM, $additionalDamage);
                    $victim->attack($event);
                }
                $attacker->getWorld()->addParticle($victim->getPosition(), new FlameParticle());
            }
        }
    }
}