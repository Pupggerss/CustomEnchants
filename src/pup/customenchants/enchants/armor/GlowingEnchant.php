<?php


namespace pup\customenchants\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pup\customenchants\types\ToggledArmorEnchant;

class GlowingEnchant extends ToggledArmorEnchant
{

    public function onEquip(Player $player, Item $item)
    : void
    {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), PHP_INT_MAX, $item->getEnchantment($this)->getLevel()));
    }

    public function onDequip(Player $player, Item $item)
    : void
    {
        if ($player->getEffects()->has(VanillaEffects::NIGHT_VISION())) {
            $player->getEffects()->remove(VanillaEffects::NIGHT_VISION());
        }
    }
}