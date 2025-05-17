<?php


namespace pup\customenchants\enchants\tools\pickaxe;

use pocketmine\block\Block;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\ToolEnchant;

class FeedEnchant extends ToolEnchant
{

    public function execute(Player $player, Item $item, Block $block, ?Event $event)
    : void
    {
            if ($player->getHungerManager()->getFood() < 20) {
                $player->getHungerManager()->setFood(min(20, $player->getHungerManager()->getFood() + random_int(1, 5)));
        }
    }
}