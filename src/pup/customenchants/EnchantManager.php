<?php


namespace pup\customenchants;


use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Armor;
use pocketmine\item\Axe;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\Hoe;
use pocketmine\item\Item;
use pup\customenchants\enchants\armor\{boots\BunnyEnchant,
    boots\GearsEnchant,
    helmet\GlowingEnchant,
    OverloadEnchant};
use pup\customenchants\enchants\sword\{AronistEnchant, BlindEnchant, DazeEnchant, ZuesEnchant};
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;
use pup\customenchants\enchants\bow\TeleportEnchant;
use pup\customenchants\enchants\tools\hoe\SpeedEnchant;
use pup\customenchants\enchants\tools\pickaxe\{DrillEnchant, FeedEnchant, HasteEnchant};
use pup\customenchants\items\ItemRegistry;
use pup\customenchants\types\WeaponEnchant;
use pup\customenchants\utils\Rarity;
use RuntimeException;

final class EnchantManager
{
    public const  IDS = [
        //Start this at 1k cus wtf bedrock!
        //Seems 1k is too much?
        'feed'     => 1000,
        'haste'    => 1001,
        'drill'    => 1002,
        'speed'    => 1003,
        'zues'     => 1004,
        'daze'     => 1005,
        'blind'    => 1006,
        'aronist'  => 1007,
        'teleport' => 1008,
        'overload' => 1009,
        'glowing'  => 1010,
        'gears'    => 1011,
        'bunny'    => 1012
    ];

    private static array $class_map = [
        'feed' => ['class' => FeedEnchant::class, 'flags' => [ItemFlags::PICKAXE]],
        'drill' => ['class' => DrillEnchant::class, 'flags' => [ItemFlags::PICKAXE]],
        'haste' => ['class' => HasteEnchant::class, 'flags' => [ItemFlags::PICKAXE]],
        'speed' => ['class' => SpeedEnchant::class, 'flags' => [ItemFlags::HOE, ItemFlags::PICKAXE]],
        'teleport' => ['class' => TeleportEnchant::class, 'flags' => [ItemFlags::BOW]],
        'bunny' => ['class' => BunnyEnchant::class, 'flags' => [ItemFlags::FEET]],
        'gears' => ['class' => GearsEnchant::class, 'flags' => [ItemFlags::FEET]],
        'glowing' => ['class' => GlowingEnchant::class, 'flags' => [ItemFlags::HEAD]],
        'overload' => ['class' => OverloadEnchant::class, 'flags' => [ItemFlags::ARMOR]],
        'aronist' => ['class' => AronistEnchant::class, 'flags' => [ItemFlags::SWORD, ItemFlags::AXE]],
        'blind' => ['class' => BlindEnchant::class, 'flags' => [ItemFlags::SWORD, ItemFlags::AXE]],
        'daze' => ['class' => DazeEnchant::class, 'flags' => [ItemFlags::SWORD]],
        'zues' => ['class' => ZuesEnchant::class, 'flags' => [ItemFlags::SWORD, ItemFlags::AXE]],
    ];

    private array $enchant_data;

    public function __construct()
    {
        $this->enchant_data = json_decode(file_get_contents(Main::getInstance()->getDataFolder() . "enchantments.json"), true);
        $this->initEnchants();
        ItemRegistry::init();
    }

    public function initEnchants(): void
    {
        $enchantmentsFile = Main::getInstance()->getDataFolder() . "enchantments.json";
        $enchantmentsData = json_decode(file_get_contents($enchantmentsFile), true) ?? [];
        $updated = false;

        foreach (self::IDS as $name => $id) {
            try {
                if (!isset($enchantmentsData[$name])) {
                    $enchantmentsData[$name] = $this->createDefaultEnchantmentData($name);
                    $updated = true;
                    Main::getInstance()->getLogger()->info("Added missing enchantment data for $name");
                }

                if ($enchant = $this->createConfiguredEnchant($name)) {
                    EnchantmentIdMap::getInstance()->register($id, $enchant);
                    Main::getInstance()->getLogger()->info("Registered {$enchant->getName()}");
                }
            } catch (RuntimeException $e) {
                Main::getInstance()->getLogger()->error($e->getMessage());
            }
        }

        if ($updated) {
            file_put_contents($enchantmentsFile, json_encode($enchantmentsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->enchant_data = $enchantmentsData;
        }
    }

    private function createDefaultEnchantmentData(string $name): array
    {
        $mapping = self::$class_map[$name] ?? [];

        return [
            'enabled' => true,
            'display_name' => ucfirst($name),
            'description' => 'Auto-generated enchantment (Please change!)',
            'rarity' => 'COMMON',
            'max_level' => 1,
            'has_chance' => false,
            'base_chance' => 0.1,
            //Add => ? 'flags' => $mapping['flags'] ?? []
        ];
    }

    private function createConfiguredEnchant(string $name): CustomEnchant|WeaponEnchant|null
    {
        $data = $this->enchant_data[$name] ?? $this->createDefaultEnchantmentData($name);

        $mapping = self::$class_map[$name] ?? null;

        if (!$mapping) {
            throw new RuntimeException("Unknown enchant type or missing mapping: $name");
        }

        $className = $mapping['class'];
        $flags = $mapping['flags'] ?? [];
        if (!class_exists($className)) {
            throw new RuntimeException("Enchant class not found: $className for enchant type: $name");
        }

        $constructorArgs = [
            $data['display_name'] ?? $name,
            Rarity::fromName($data['rarity']),
            $data['description'] ?? '',
            $data['max_level'] ?? 1,
            ...$flags
        ];

        $enchant = new $className(...$constructorArgs);

        //Chance is on the enchant whether the base chance is set.
        if ($data['has_chance'] ?? false) {
            $enchant->setBaseChance($data['base_chance'] ?? 0.1);
        }

        return $enchant;
    }

    public static function loreItem(Item $item): Item
    {
        //TODO: Update Lores
        if ($item->getNamedTag()->getTag("hideEnchantments") !== null) {
            return $item;
        }

        $config = Main::getInstance()->getConfig();
        $enchantLore = [];
        $currentLore = $item->getLore();

        foreach ($item->getEnchantments() as $enchantmentInstance) {
            $enchantment = $enchantmentInstance->getType();

            $rarity = $enchantment->getRarity();
            $color = Rarity::getColor($rarity);
            $level = $enchantmentInstance->getLevel();
            $name = $enchantment->getName();
            if($name instanceof Translatable){
                $name = $name->getText();
            }

            $levelDisplay = $config->getNested('enchant_lore.roman_numerals', true)
                ? self::intToRoman($level)
                : $level;

            $entry = TextFormat::colorize(str_replace(
                ['{color}', '{name}', '{level}'],
                [$color, $name, $levelDisplay],
                $config->getNested('enchant_lore.entry_format', "&r&8» &r{color}{name} {level}")
            ));

            $enchantLore[$entry] = $rarity;
        }

        if ($config->getNested('enchant_lore.sort_by_rarity', true) && !empty($enchantLore)) {
            asort($enchantLore);
            $enchantLore = array_keys($enchantLore);
        }

        $newLore = [];
        if (!empty($enchantLore)) {
            $header = TextFormat::colorize($config->getNested('enchant_lore.header', "&r&dEnchantments:"));
            array_unshift($enchantLore, $header);
            $newLore = $enchantLore;
        }

        $finalLore = array_merge($currentLore, $newLore);

        $finalLore = array_filter($finalLore, function($line) {
            return is_string($line) && trim($line) !== '';
        });

        $item->setLore(array_values($finalLore));
        return $item;
    }
    public static function intToRoman($number): string
    {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    private static function getItemTypeFlags(Item $item) : int {
        if($item instanceof Sword) {
            return ItemFlags::SWORD;
        }
        if($item instanceof Bow) {
            return ItemFlags::BOW;
        }
        if($item instanceof Pickaxe) {
            return ItemFlags::PICKAXE;
        }
        if($item instanceof Axe) {
            return ItemFlags::AXE;
        }
        if($item instanceof Shovel) {
            return ItemFlags::SHOVEL;
        }
        if($item instanceof Hoe) {
            return ItemFlags::HOE;
        }
        if($item instanceof Tool) {
            return ItemFlags::DIG;
        }

        if ($item instanceof Armor) {
                $itemTypeId = $item->getTypeId();
                // Helmets
                if(in_array($itemTypeId, [
                    VanillaItems::LEATHER_CAP()->getTypeId(),
                    VanillaItems::GOLDEN_HELMET()->getTypeId(),
                    VanillaItems::CHAINMAIL_HELMET()->getTypeId(),
                    VanillaItems::IRON_HELMET()->getTypeId(),
                    VanillaItems::DIAMOND_HELMET()->getTypeId(),
                    VanillaItems::NETHERITE_HELMET()->getTypeId()
                ])) {
                    return ItemFlags::HEAD;
                }
                // Chestplates
                if(in_array($itemTypeId, [
                    VanillaItems::LEATHER_TUNIC()->getTypeId(),
                    VanillaItems::GOLDEN_CHESTPLATE()->getTypeId(),
                    VanillaItems::CHAINMAIL_CHESTPLATE()->getTypeId(),
                    VanillaItems::IRON_CHESTPLATE()->getTypeId(),
                    VanillaItems::DIAMOND_CHESTPLATE()->getTypeId(),
                    VanillaItems::NETHERITE_CHESTPLATE()->getTypeId()
                ])) {
                    return ItemFlags::TORSO;
                }
                // Leggings
                if(in_array($itemTypeId, [
                    VanillaItems::LEATHER_PANTS()->getTypeId(),
                    VanillaItems::GOLDEN_LEGGINGS()->getTypeId(),
                    VanillaItems::CHAINMAIL_LEGGINGS()->getTypeId(),
                    VanillaItems::IRON_LEGGINGS()->getTypeId(),
                    VanillaItems::DIAMOND_LEGGINGS()->getTypeId(),
                    VanillaItems::NETHERITE_LEGGINGS()->getTypeId()
                ])) {
                    return ItemFlags::LEGS;
                }
                // Boots
                if(in_array($itemTypeId, [
                    VanillaItems::LEATHER_BOOTS()->getTypeId(),
                    VanillaItems::GOLDEN_BOOTS()->getTypeId(),
                    VanillaItems::CHAINMAIL_BOOTS()->getTypeId(),
                    VanillaItems::IRON_BOOTS()->getTypeId(),
                    VanillaItems::DIAMOND_BOOTS()->getTypeId(),
                    VanillaItems::NETHERITE_BOOTS()->getTypeId()
                ])) {
                    return ItemFlags::FEET;
                }
        }

        return ItemFlags::NONE;
    }

    public static function canApplyEnchant(string $enchantName, Item $item): bool {
        $enchantName = strtolower(str_replace(" ", "", $enchantName));
        $mapping = self::$class_map[$enchantName] ?? null;
        if ($mapping === null) {
            return false;
        }

        $requiredFlags = $mapping['flags'] ?? [];
        if (empty($requiredFlags)) {
            return true;
        }

        $itemTypeFlags = self::getItemTypeFlags($item);

        if ($item instanceof Armor) {
            foreach ($requiredFlags as $requiredFlag) {
                if ($requiredFlag === $itemTypeFlags) {
                    return true;
                }
            }
        }

        foreach ($requiredFlags as $requiredFlag) {
            if (($itemTypeFlags & $requiredFlag) !== 0) {
                return true;
            }
        }

        return false;
    }

    public static function nameToId(string $name)
    : int
    {
        return self::IDS[strtolower($name)] ?? -1;
    }

    public static function idToEnchant(int $id)
    : ?Enchantment
    {
        return EnchantmentIdMap::getInstance()->fromId($id);
    }
}