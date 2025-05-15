<?php

namespace pup\customenchants;

final class Rarity
{
    private static array $rarities = [];

    public const COMMON = 1;
    public const UNCOMMON = 2;
    public const RARE = 3;
    public const EPIC = 4;
    public const LEGENDARY = 5;
    public const MASTERY = 6;
    public const HEROIC = 7;

    public static function init(array $config): void
    {
        //TODO: Add all the rarities :(
        self::$rarities = [
            self::COMMON => [
                'name' => 'COMMON',
                'color' => $config['COMMON']['display_color'] ?? '§f',
                'weight' => $config['COMMON']['weight'] ?? 50, //For books!
                'enabled' => $config['COMMON']['enabled'] ?? true //TODO: add checks!
            ]];
    }

    public static function isEnabled(int $rarity): bool
    {
        return self::$rarities[$rarity]['enabled'] ?? false;
    }

    public static function getEnabledRarities(): array
    {
        return array_filter(self::$rarities, fn($r) => $r['enabled']);
    }

    public static function getColor(int $rarity): string
    {
        return self::$rarities[$rarity]['color'] ?? '§f';
    }

    public static function fromName(string $name): int
    {
        //Defaults to common so you cant really use any other rarities :p
        return constant("self::$name") ?? self::COMMON;
    }
}