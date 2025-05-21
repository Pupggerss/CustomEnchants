<?php


namespace pup\customenchants;

use pocketmine\plugin\PluginBase;
use pup\customenchants\utils\Rarity;


class Main extends PluginBase
{
    private static self $instance;

    public static function getInstance()
    : Main
    {
        return self::$instance;
    }

    public function onLoad()
    : void
    {
        self::$instance = $this;
    }

    public function onEnable()
    : void
    {

        $this->saveResource("enchantments.json");
        $this->saveDefaultConfig();
        Rarity::init($this->getConfig()->get("rarities"));

        $this->getServer()->getPluginManager()->registerEvents(new EnchantListener(), $this);
        $this->getServer()->getCommandMap()->register("CustomEnchants", new EnchanterComand());

        new EnchantManager();
    }
}