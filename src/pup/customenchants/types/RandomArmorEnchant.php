<?php


namespace pup\customenchants\types;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\player\Player;

abstract class RandomArmorEnchant extends Enchantment
{
    use ChanceTriat;

    public static function onDamage(EntityDamageEvent $event)
    : void
    {
        $player = $event->getEntity();
        if (!$player instanceof Player) {
            return;
        }
        $damager = null;
        if ($event instanceof EntityDamageByEntityEvent && $event->getDamager() instanceof Player) {
            $damager = $event->getDamager();
        }
        foreach ($player->getArmorInventory()->getContents() as $armor) {
            foreach ($armor->getEnchantments() as $enchant) {
                if ($armor instanceof Armor && ($enchant = $enchant->getType()) instanceof self) {
                    $enchant->execute($player, $damager, $armor);
                }
            }
        }
    }

    abstract public function execute(Player $player, ?Player $damager, Armor $armor)
    : void;

}