<?php


namespace pup\customenchants\enchants\bow;


use pocketmine\entity\projectile\Arrow;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\BowEnchant;

class TeleportEnchant extends BowEnchant
{

    public function execute(Player $player, Item $item, Arrow $arrow)
    : void
    {
        if ($player->getWorld() !== null && $arrow->getWorld() !== null) {
            if ($player->getWorld() === $arrow->getWorld()) {
                $player->teleport($arrow->getPosition());
            }
        }
    }
}