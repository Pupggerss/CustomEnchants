<?php


namespace pup\customenchants\types;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\CallbackInventoryListener;
use pocketmine\inventory\Inventory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\player\Player;

abstract class ToggledArmorEnchant extends Enchantment
{
    public static array $armorListeners = [];

    public static function onToggle(InventoryTransactionEvent $event)
    : void
    {
        $player = $event->getTransaction()->getSource();
        if (!isset(self::$armorListeners[$player->getName()])) {
            $listener = new CallbackInventoryListener(
                $run = static function (Inventory $inventory, int $slot, Item $old) use ($player)
                : void {
                    $newItem = $inventory->getItem($slot);
                    if (!$newItem->equals($old, false)) {
                        foreach ($newItem->getEnchantments() as $enchantment) {
                            $enchantment = $enchantment->getType();
                            if ($enchantment instanceof self) {
                                $enchantment->onEquip($player, $newItem);
                            }
                        }
                        foreach ($old->getEnchantments() as $enchantment) {
                            $enchantment = $enchantment->getType();
                            if ($enchantment instanceof self) {
                                $enchantment->onDequip($player, $old);
                            }
                        }
                    }
                },
                function (Inventory $inventory, array $oldContents) use ($player, $run)
                : void {
                    foreach ($oldContents as $slot => $item) {
                        if (!$item->equals($inventory->getItem($slot), false)) {
                            $run($inventory, $slot, $item);
                        }
                    }
                }
            );
            self::$armorListeners[$player->getName()] = $listener;
            $player->getArmorInventory()->getListeners()->add($listener);
        }
    }


    abstract public function onEquip(Player $player, Item $item)
    : void;

    abstract public function onDequip(Player $player, Item $item)
    : void;

    public static function removeArmorListener(Player $player)
    : void
    {
        $playerName = $player->getName();
        if (isset(self::$armorListeners[$playerName])) {
            $player->getArmorInventory()->getListeners()->remove(self::$armorListeners[$playerName]);
            unset(self::$armorListeners[$playerName]);
        }
    }

}