<?php


namespace pup\customenchants\enchants\tools\hoe;


use pocketmine\block\Block;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\ToolEnchant;

class SpeedEnchant extends ToolEnchant
{

    public function execute(Player $player, Item $item, Block $block)
    : void
    {
        $level = $item->getEnchantment($this)->getLevel();

        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 255, min($level - 1, 1)));
    }
}