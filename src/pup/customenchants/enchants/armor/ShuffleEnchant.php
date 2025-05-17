<?php


namespace pup\customenchants\enchants\armor;


use pocketmine\inventory\Inventory;
use pocketmine\item\Armor;
use pocketmine\network\mcpe\protocol\types\Enchant;
use pocketmine\player\Player;
use pocketmine\world\sound\EndermanTeleportSound;
use pup\customenchants\types\RandomArmorEnchant;

class ShuffleEnchant extends RandomArmorEnchant
{

    public function execute(Player $player, ?Player $damager, Armor $armor): void {
        foreach ($armor->getEnchantments() as $enchantment) {
            if ($enchantment instanceof self || $enchantment instanceof Enchant) {
                $chance = $this->getChance($enchantment->getLevel(), $enchantment->getMaxLevel());

                if ($damager !== null && mt_rand(1, 100) <= $chance) {
                    $this->shuffleInventory($damager->getInventory());
                }
            }
        }
    }

    private function shuffleInventory(Inventory $inventory): void {
        $hotbarContents = [];
        for ($slot = 0; $slot <= 8; $slot++) {
            $item = $inventory->getItem($slot);
            if (!$item->isNull()) {
                $hotbarContents[$slot] = $item;
            }
        }

        if (count($hotbarContents) > 1) {
            $slots = array_keys($hotbarContents);
            $items = array_values($hotbarContents);
            shuffle($items);

            foreach ($slots as $index => $slot) {
                $inventory->setItem($slot, $items[$index]);
            }

            if ($inventory->getHolder() instanceof Player) {
                $inventory->getHolder()->getWorld()->addSound(
                    $inventory->getHolder()->getPosition(),
                    new EndermanTeleportSound()
                );
            }
        }
    }

}