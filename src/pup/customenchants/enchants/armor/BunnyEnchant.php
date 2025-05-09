<?php


namespace pup\customenchants\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\ToggledArmorEnchant;

class BunnyEnchant extends ToggledArmorEnchant
{

    public function onEquip(Player $player, Item $item)
    : void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), PHP_INT_MAX, $item->getEnchantment($this)->getLevel()));
    }

    public function onDequip(Player $player, Item $item)
    : void
    {
        if ($player->getEffects()->has(VanillaEffects::JUMP_BOOST())) {
            $player->getEffects()->remove(VanillaEffects::JUMP_BOOST());
        }
    }
}