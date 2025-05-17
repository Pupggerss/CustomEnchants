<?php


namespace pup\customenchants\enchants\armor\boots;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\ToggledArmorEnchant;

class GearsEnchant extends ToggledArmorEnchant
{

    public function onEquip(Player $player, Item $item)
    : void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 2147483647, min($item->getEnchantment($this)->getLevel() - 1, 1)));
    }

    public function onDequip(Player $player, Item $item)
    : void
    {
        if ($player->getEffects()->has(VanillaEffects::SPEED())) {
            $player->getEffects()->remove(VanillaEffects::SPEED());
        }
    }
}