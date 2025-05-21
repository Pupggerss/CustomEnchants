<?php

namespace pup\customenchants\utils;

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
        self::$rarities = [
            self::COMMON => [
                'name' => 'COMMON',
                'color' => $config[self::COMMON]['display_color'] ?? '§f',
                'weight' => $config[self::COMMON]['weight'] ?? 50,
                'enabled' => $config[self::COMMON]['enabled'] ?? true
            ],
            self::UNCOMMON => [
                'name' => 'UNCOMMON',
                'color' => $config[self::UNCOMMON]['display_color'] ?? '§a',
                'weight' => $config[self::UNCOMMON]['weight'] ?? 25,
                'enabled' => $config[self::UNCOMMON]['enabled'] ?? true
            ],
            self::RARE => [
                'name' => 'RARE',
                'color' => $config[self::RARE]['display_color'] ?? '§9',
                'weight' => $config[self::RARE]['weight'] ?? 15,
                'enabled' => $config[self::RARE]['enabled'] ?? true
            ],
            self::EPIC => [
                'name' => 'EPIC',
                'color' => $config[self::EPIC]['display_color'] ?? '§5',
                'weight' => $config[self::EPIC]['weight'] ?? 10,
                'enabled' => $config[self::EPIC]['enabled'] ?? true
            ],
            self::LEGENDARY => [
                'name' => 'LEGENDARY',
                'color' => $config[self::LEGENDARY]['display_color'] ?? '§6',
                'weight' => $config[self::LEGENDARY]['weight'] ?? 5,
                'enabled' => $config[self::LEGENDARY]['enabled'] ?? true
            ],
            self::MASTERY => [
                'name' => 'MASTERY',
                'color' => $config[self::MASTERY]['display_color'] ?? '§3',
                'weight' => $config[self::MASTERY]['weight'] ?? 3,
                'enabled' => $config[self::MASTERY]['enabled'] ?? true
            ],
            self::HEROIC => [
                'name' => 'HEROIC',
                'color' => $config[self::HEROIC]['display_color'] ?? '§4',
                'weight' => $config[self::HEROIC]['weight'] ?? 0,
                'enabled' => $config[self::HEROIC]['enabled'] ?? true
            ]
        ];
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