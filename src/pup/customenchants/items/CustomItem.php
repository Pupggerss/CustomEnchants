<?php

namespace pup\customenchants\items;

use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

abstract class CustomItem
{
    protected const CUSTOM_ITEM_ID = "customItem";
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly array  $description,
        private readonly Item   $item
    )
    {
        $this->item->setCustomName(TextFormat::RESET . $this->name);

        $formattedDescription = array_map(
            fn(string $line) => TextFormat::RESET . TextFormat::GRAY . $line,
            $this->description
        );

        $this->item->setLore($formattedDescription);
    }

    public function getName()
    : string
    {
        return $this->name;
    }

    public function getDescription()
    : array
    {
        return $this->description;
    }

    public function getItem()
    : Item
    {
        return clone $this->item;
    }

    public function getId()
    : string
    {
        return $this->id;
    }
}