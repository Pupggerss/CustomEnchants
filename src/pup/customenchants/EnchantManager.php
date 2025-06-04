<?php


namespace pup\customenchants;


use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\ItemFlags; //To be changed
use pup\customenchants\enchants\armor\{OverloadEnchant, ShuffleEnchant};
use pup\customenchants\enchants\armor\helmet\{AquaticEnchant, GlowingEnchant};
use pup\customenchants\enchants\armor\boots\{BunnyEnchant, GearsEnchant, TakeOffEnchant};
use pup\customenchants\enchants\sword\{AronistEnchant, BlindEnchant, DazeEnchant, ZuesEnchant};
use pocketmine\item\{Axe, Hoe, Shovel, Tool, VanillaItems, Pickaxe, Sword, Item, Bow, Armor};
use pup\customenchants\enchants\bow\TeleportEnchant;
use pup\customenchants\enchants\tools\hoe\SpeedEnchant;
use pup\customenchants\enchants\tools\pickaxe\{AutoSmeltEnchant, DrillEnchant, FeedEnchant, HasteEnchant};
use pup\customenchants\enchants\tools\RestoreEnchant;
use pup\customenchants\types\WeaponEnchant;
use pup\customenchants\utils\Rarity;
use RuntimeException;
final class EnchantManager
{
    public const array IDS = [
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
        'bunny'    => 1012,
        'restore'  => 1013,
        'shuffle' =>  1014,
        'takeoff' =>  1015,
        'aquatic' =>  1016,
        'autosmelt' => 1017
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
        'aronsit' => ['class' => AronistEnchant::class, 'flags' => [ItemFlags::SWORD, ItemFlags::AXE]],
        'blind' => ['class' => BlindEnchant::class, 'flags' => [ItemFlags::SWORD, ItemFlags::AXE]],
        'daze' => ['class' => DazeEnchant::class, 'flags' => [ItemFlags::SWORD]],
        'zues' => ['class' => ZuesEnchant::class, 'flags' => [ItemFlags::SWORD, ItemFlags::AXE]],
        'restore' => ['class' => RestoreEnchant::class, 'flags' => [ItemFlags::DIG, ItemFlags::PICKAXE]],
        'shuffle' => ['class' => ShuffleEnchant::class, 'flags' => [ItemFlags::ARMOR]],
        'takeoff' => ['class' => TakeOffEnchant::class, 'flags' => [ItemFlags::FEET]],
        'aquatic' => ['class' => AquaticEnchant::class, 'flags' => [ItemFlags::HEAD]],
        'autosmelt' => ['class' => AutoSmeltEnchant::class, 'flags' => [ItemFlags::DIG]]
    ];

    private array $enchant_data;

    public function __construct()
    {
        $this->enchant_data = json_decode(file_get_contents(Main::getInstance()->getDataFolder() . "enchantments.json"), true);
        $this->initEnchants();
    }

    public function initEnchants(): void
    {
        foreach (self::IDS as $name => $id) {
            try {
                if ($enchant = $this->createConfiguredEnchant($name)) {
                    EnchantmentIdMap::getInstance()->register($id, $enchant);
                    Main::getInstance()->getLogger()->info("Added {$enchant->getName()}");
                }
            } catch (RuntimeException $e) {
                Main::getInstance()->getLogger()->error($e->getMessage());
            }
        }
    }

    private function createConfiguredEnchant(string $name): CustomEnchant|WeaponEnchant|null
    {
        $data = $this->enchant_data[$name] ?? null;
        if (!$data || !($data['enabled'] ?? true)) return null;

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

        if ($data['has_chance'] ?? false) {
            $enchant->setBaseChance($data['chance']);
        }

        return $enchant;
    }

    public static function loreItem(Item $item): Item {
        if (!is_null($item->getNamedTag()->getTag("hideEnchantments"))) {
            return $item;
        }

        $config = Main::getInstance()->getConfig();

        $enchantLore = [];
        foreach ($item->getEnchantments() as $enchantmentInstance) {
            $enchantment = $enchantmentInstance->getType();
            if (!$enchantment instanceof CustomEnchant) {
                continue;
            }

            $rarity = $enchantment->getRarity();
            $color = Rarity::getColor($rarity);
            if($config->get("enchant_lore.roman_numerals", true)){
                $level = self::intToRoman($enchantmentInstance->getLevel());
            } else {
                $level = $enchantmentInstance->getLevel();
            }

            $formattedEntry = str_replace(
                ['{color}', '{name}', '{level}'],
                [$color, $enchantment->getName(), $level],
                $config->get("enchant_lore.entry_format", " §r§8» §r{color}{name} {level}")
            );

            $enchantLore[$formattedEntry] = $rarity;
        }

        if (!empty($enchantLore)) {
            if ($config->get("enchant_lore.sort_by_rarity", true)) {
                asort($enchantLore);
            }

            $lore = [$config->get("enchant_lore.header", "§r§dEnchantments:")];
            $lore = array_merge($lore, array_keys($enchantLore));
            $item->setLore($lore);
        }

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
    public static function canApplyEnchant(string $enchantName, Item $item): bool
    {
        $enchantName = strtolower(str_replace(" ", "", $enchantName));
        $mapping = self::$class_map[$enchantName] ?? null;
        if (!$mapping) {
            return false;
        }

        $requiredFlags = $mapping['flags'] ?? [];
        if (empty($requiredFlags)) {
            return true;
        }

        $itemTypeFlags = self::getItemTypeFlags($item);

        foreach ($requiredFlags as $requiredFlag) {
            if (($itemTypeFlags & $requiredFlag) === 0) {
                return false;
            }
        }
        return true;
    }
}