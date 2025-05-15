<?php


namespace pup\customenchants;


use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\Item;
use pup\customenchants\enchants\armor\{BunnyEnchant, GearsEnchant, GlowingEnchant, OverloadEnchant};
use pup\customenchants\enchants\sword\{AronistEnchant, BlindEnchant, DazeEnchant, ZuesEnchant};
use pup\customenchants\enchants\bow\TeleportEnchant;
use pup\customenchants\enchants\tools\hoe\SpeedEnchant;
use pup\customenchants\enchants\tools\pickaxe\{DrillEnchant, FeedEnchant, HasteEnchant};
use RuntimeException;

final class EnchantManager
{
    public const IDS = [
        //Start this at 1k cus wtf bedrock!
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
                }
            } catch (RuntimeException $e) {
                Main::getInstance()->getLogger()->error($e->getMessage());
            }
        }
    }

    private function createConfiguredEnchant(string $name): ?CustomEnchant
    {
        $data = $this->enchant_data[$name] ?? null;
        if (!$data || !($data['enabled'] ?? true)) return null;

        $enchant = match ($name) {
            'feed' => new FeedEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::PICKAXE,
            ),
            'drill' => new DrillEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::PICKAXE
            ),
            'haste' => new HasteEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::PICKAXE
            ),
            'speed' => new SpeedEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::HOE,
                ItemFlags::PICKAXE
            ),
            'teleport' => new TeleportEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::BOW
            ),
            'bunny' => new BunnyEnchant(
              $data['display_name'],
              Rarity::fromName($data['rarity']),
              $data['description'],
              $data['max_level'],
              ItemFlags::FEET
            ),
            'gears' => new GearsEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::FEET
            ),
            'glowing' => new GlowingEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::HEAD
            ),
            'overload' => new OverloadEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::ARMOR
            ),
            'aronsit' => new AronistEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::SWORD,
                ItemFlags::AXE
            ),
            'blind' => new BlindEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::SWORD,
                ItemFlags::AXE
            ),
            'daze' => new DazeEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::SWORD
            ),
            'zues' => new ZuesEnchant(
                $data['display_name'],
                Rarity::fromName($data['rarity']),
                $data['description'],
                $data['max_level'],
                ItemFlags::SWORD,
                ItemFlags::AXE
            ),
            default => throw new RuntimeException("Unknown enchant type: $name")
        };

        if ($data['has_chance'] ?? false) {
            $enchant->setBaseChance($data['base_chance'] ?? 0.1);
        }

        return $enchant;
    }

    /*
     * @deprecated
     */
    public static function loreItem(Item $item)
    : Item
    {
        if (!is_null($item->getNamedTag()->getTag("hideEnchantments"))) {
            return $item;
        }

        $enchantLore = [];
        foreach ($item->getEnchantments() as $enchantmentInstance) {
            $enchantment = $enchantmentInstance->getType();
            $rarity = $enchantment->getRarity();
            $color = Rarity::getColor($rarity);

            $enchantLore[" §r§8» §r" . $color . $enchantment->getName() . " " . self::intToRoman($enchantmentInstance->getLevel())] = $rarity;
        }
        asort($enchantLore);
        $lore = ["§r§dEnchantments:"];
        $lore = array_merge($lore, array_keys($enchantLore));
        $item->setLore($lore);
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

    private function getRarity(string $enchantName): int
    {
        $rarityName = $this->enchantConfig[$enchantName]['rarity'] ?? 'COMMON';
        return Rarity::fromName($rarityName);
    }
}