<?php


namespace pup\customenchants;


use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\lang\Translatable;
use pocketmine\utils\Config;
use pup\customenchants\types\enchants\armor\{BunnyEnchant, GearsEnchant, GlowingEnchant, OverloadEnchant};
use pup\customenchants\types\enchants\sword\{AronistEnchant, BlindEnchant, DazeEnchant, ZuesEnchant};
use pup\customenchants\types\enchants\tools\hoe\SpeedEnchant;
use pup\customenchants\types\enchants\tools\pickaxe\{DrillEnchant, FeedEnchant, HasteEnchant};

final class EnchantManager
{
    private array $max_levels;

    public const ID = [
        //Start this at 1k cus wtf bedrock!
        'feed' => 1000,
        'haste' => 1001,
        'drill' => 1002,
        'speed' => 1003,
        'zues' => 1004,
        'daze' => 1005,
        'blind' => 1006,
        'aronist' => 1007,
        'teleport' => 1008,
        'overload' => 1009,
        'glowing' => 1010,
        'gears' => 1011,
        'bunny' => 1012
    ];

    public const RARITY_TO_COLOR = [
        Rarity::COMMON => "§a",
        Rarity::UNCOMMON => "§2",
        Rarity::RARE => "§6",
        Rarity::MYTHIC => "§4",
    ];

    public function __construct(private Config $config){
        $this->max_levels = $$this->config->getAll();
        $this->initEnchants();
    }

    public function initEnchants(){
        $ces = [
        self::ID['feed'] => new FeedEnchant("Feed", Rarity::UNCOMMON, ItemFlags::PICKAXE, ItemFlags::NONE, $this->max_levels['tool_levels']['feed']),
        self::ID['haste'] => new HasteEnchant("Haste", Rarity::COMMON, ItemFlags::PICKAXE, ItemFlags::AXE, $this->max_levels['tool_levels']['haste']),
        self::ID['drill'] => new DrillEnchant("Drill", Rarity::COMMON, ItemFlags::PICKAXE, ItemFlags::NONE, $this->max_levels['tool_levels']['drill']),
        self::ID['speed'] => new SpeedEnchant("Speed", Rarity::COMMON, ItemFlags::HOE, ItemFlags::NONE, $this->max_levels['tool_levels']['speed']),
        self::ID['zues'] => new ZuesEnchant("Zues", Rarity::RARE, ItemFlags::SWORD, ItemFlags::AXE, $this->max_levels['sword_levels']['zues']),
        self::ID['daze'] => new DazeEnchant("Daze", Rarity::COMMON, ItemFlags::SWORD, ItemFlags::AXE, $this->max_levels['sword_levels']['daze']),
        self::ID['blind'] => new BlindEnchant("Blind", Rarity::COMMON, ItemFlags::SWORD, ItemFlags::AXE, $this->max_levels['sword_levels']['blind']),
        self::ID['aronist'] => new AronistEnchant("Aronist", Rarity::UNCOMMON, ItemFlags::SWORD, ItemFlags::NONE, $this->max_levels['sword_levels']['aronist']),
        self::ID['overload'] => new OverloadEnchant("Overload", Rarity::RARE, ItemFlags::ARMOR, ItemFlags::NONE, $this->max_levels['armor_levels']['overload']),
        self::ID['glowing'] => new GlowingEnchant("Glowing", Rarity::UNCOMMON, ItemFlags::HEAD, ItemFlags::NONE, $this->max_levels['armor_levels']['glowing']),
        self::ID['gears'] => new GearsEnchant("Gears", Rarity::COMMON, ItemFlags::FEET, ItemFlags::NONE, $this->max_levels['armor_levels']['gears']),
        self::ID['bunny'] => new BunnyEnchant("Bunny", Rarity::UNCOMMON, ItemFlags::FEET, ItemFlags::NONE, $this->max_levels['armor_levels']['bunny'])
            ];

        foreach ($ces as $id => $enchant){
            EnchantmentIdMap::getInstance()->register($id, $enchant);
        }
    }

    public static function loreItem(Item $item): Item
    {
        if (!is_null($item->getNamedTag()->getTag("hideEnchantments"))) {
            return $item;
        }

        $enchantLore = [];
        foreach ($item->getEnchantments() as $enchantmentInstance) {
            $enchantment = $enchantmentInstance->getType();
            $rarity = $enchantment->getRarity();
            $name = $enchantment->getName();
            $level = $item->getEnchantmentLevel($enchantment);

            if ($name instanceof Translatable) {
                $name = $name->getText();
            }

            $enchantLore[" §r§8» §r" . self::RARITY_TO_COLOR[$rarity] . $name . " " . Tools::intToRoman($level)] = $rarity;
        }
        asort($enchantLore);
        $lore = ["§r§dEnchantments:"];
        $lore = array_merge($lore, array_keys($enchantLore));
        $item->setLore($lore);
        return $item;
    }
}