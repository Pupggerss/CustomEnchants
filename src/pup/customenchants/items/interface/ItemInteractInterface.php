<?php

namespace pup\customenchants\items\interface;

use pocketmine\item\Item;
use pocketmine\player\Player;

interface ItemInteractInterface
{
    public function onInteract(Item $item, Player $player);
}