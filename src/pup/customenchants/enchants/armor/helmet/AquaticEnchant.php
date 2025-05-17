<?php


namespace pup\customenchants\enchants\armor\helmet;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\ToggledArmorEnchant;

class AquaticEnchant extends ToggledArmorEnchant
{

    public function onEquip(Player $player, Item $item)
    : void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::WATER_BREATHING(), 2147483647, 1));
    }

    public function onDequip(Player $player, Item $item)
    : void
    {
        if($player->getEffects()->has(VanillaEffects::WATER_BREATHING())){
            $player->getEffects()->remove(VanillaEffects::WATER_BREATHING());
        }
    }
}