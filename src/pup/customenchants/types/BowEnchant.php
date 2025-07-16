<?php


namespace pup\customenchants\types;


use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\CustomEnchant;

abstract class BowEnchant extends CustomEnchant
{
    public static function onShoot(EntityShootBowEvent $event)
    : void
    {
        //NOOP
    }

    public static function onHitBlock(ProjectileHitBlockEvent $event)
    : void
    {
        $arrow = $event->getEntity();
        if (!$arrow instanceof Arrow) {
            return;
        }
        $player = $arrow->getOwningEntity();
        if ($player instanceof Player) {
            $item = $player->getInventory()->getItemInHand();
            foreach ($item->getEnchantments() as $enchant) {
                if (($enchant = $enchant->getType()) instanceof self) {
                    $enchant->execute($player, $item, $arrow);
                }
            }
        }
    }

    abstract public function execute(Player $player, Item $item, Arrow $arrow)
    : void;
}