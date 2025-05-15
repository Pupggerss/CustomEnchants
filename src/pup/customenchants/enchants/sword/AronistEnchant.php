<?php


namespace pup\customenchants\enchants\sword;


use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use pocketmine\world\particle\FlameParticle;
use pup\customenchants\types\WeaponEnchant;

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

    public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel)
    : void
    {
        if ($victim instanceof Player && $attacker instanceof Player) {
            $chance = $this->calculateChance($enchantmentLevel, $this->getMaxLevel(), 5);
            if (random_int(1, 100) <= $chance) {
                while ($attacker->isOnFire()) {
                    $additionalDamage = $enchantmentLevel * 1.5;

                    $event = new EntityDamageByEntityEvent($attacker, $victim, EntityDamageByEntityEvent::CAUSE_CUSTOM, $additionalDamage);
                    $victim->attack($event);
                }
                $attacker->getWorld()->addParticle($victim->getPosition(), new FlameParticle());
            }
        }
    }
}