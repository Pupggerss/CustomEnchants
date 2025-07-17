<?php

namespace pup\customenchants\items;

use InvalidArgumentException;
use pup\customenchants\items\types\BlackScroll;
use pup\customenchants\items\types\WhiteScroll;

//TODO: Remove and use base classes
final class ItemRegistry
{
    /**
     * @var array<string, CustomItem> Registered custom items
     */
    private static array $items = [];

    public static function init()
    : void
    {
        self::register(new WhiteScroll(true));
        self::register(new BlackScroll(100));
    }

    public static function register(CustomItem $item)
    : void
    {
        self::$items[$item->getId()] = $item;
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromId(string $id)
    : CustomItem
    {
        if (!isset(self::$items[$id])) {
            throw new InvalidArgumentException("Custom item with ID {$id} not found");
        }

        return self::$items[$id];
    }

    /**
     * @return array<string, CustomItem>
     */
    public static function getAll()
    : array
    {
        return self::$items;
    }
}