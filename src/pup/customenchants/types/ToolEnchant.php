<?php


namespace pup\customenchants\types;


use JetBrains\PhpStorm\Pure;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\player\Player;

abstract class ToolEnchant extends Enchantment
{
    use ChanceTriat;

    public static function onBreak(BlockBreakEvent $event): void
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

    abstract public function execute(Player $player, Item $item, Block $block): void;

}