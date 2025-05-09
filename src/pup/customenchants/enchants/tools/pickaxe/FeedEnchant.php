<?php


namespace pup\customenchants\types\enchants\tools\pickaxe;


use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\ToolEnchant;

class FeedEnchant extends ToolEnchant
{

    public function execute(Player $player, Item $item, Block $block)
    : void
    {
        $level = $item->getEnchantment($this)->getLevel();
        $chance = $this->calculateChance($level, $this->getMaxLevel(), 5);

        if (random_int(1, 100) <= $chance){
            if($player->getHungerManager()->getFood() < 20){
                $player->getHungerManager()->setFood(min(20, $player->getHungerManager()->getFood() + random_int(1, 5)));
            }
        }
    }
}