<?php


namespace pup\customenchants\enchants\armor;


use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\ToggledArmorEnchant;

class OverloadEnchant extends ToggledArmorEnchant
{

    public function onEquip(Player $player, Item $item)
    : void
    {
        $player->setMaxHealth($player->getMaxHealth() + ($item->getEnchantment($this)->getLevel() * 2));
    }

    public function onDequip(Player $player, Item $item)
    : void
    {
        if ($player->getMaxHealth() > 20) {
            $player->setMaxHealth($player->getMaxHealth() - ($item->getEnchantment($this)->getLevel() * 2));
        }
    }
}