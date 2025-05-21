<?php


namespace pup\customenchants\enchants\armor\boots;


use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\ToggledArmorEnchant;

class TakeOffEnchant extends ToggledArmorEnchant
{

    public function onEquip(Player $player, Item $item)
    : void
    {
        $player->setAllowFlight(true);
        $player->setFlying(true);
    }

    public function onDequip(Player $player, Item $item)
    : void
    {
        if($player->getAllowFlight()){
            $player->setAllowFlight(false);
            $player->setFlying(false);
        }
    }
}