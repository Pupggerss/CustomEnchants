<?php

namespace pup\customenchants\items\interface;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\player\Player;

interface InventoryTransactionInterface
{
    public function onInventoryTransaction(InventoryTransactionEvent $e, Player $player, Item $item, Item $target, SlotChangeAction $action1, SlotChangeAction $action2)
    : void;
}