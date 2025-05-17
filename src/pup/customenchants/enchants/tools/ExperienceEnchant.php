<?php


namespace pup\customenchants\enchants\tools;


use pocketmine\block\Block;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\ToolEnchant;

class ExperienceEnchant extends ToolEnchant
{

    public function execute(Player $player, Item $item, Block $block, ?Event $event)
    : void
    {
        $level = $item->getEnchantment($this)->getLevel();
        $chance = $this->getChance($level, $this->getMaxLevel());

        if(random_int(1, 100) <= $chance){
            $player->getXpManager()->addXp((random_int(1, 10) * random_int(1, 5)), true);
        }
    }
}