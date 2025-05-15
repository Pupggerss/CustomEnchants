<?php


namespace pup\customenchants\types;


use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\CustomEnchant;

abstract class ToolEnchant extends CustomEnchant
{
    public static function onBreak(BlockBreakEvent $event)
    : void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $block = $event->getBlock();
        foreach ($item->getEnchantments() as $enchant) {
            if (($enchant = $enchant->getType()) instanceof self) {
                $enchant->execute($player, $item, $block);
            }
        }
    }

    abstract public function execute(Player $player, Item $item, Block $block)
    : void;

}