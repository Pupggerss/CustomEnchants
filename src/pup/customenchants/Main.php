<?php


namespace pup\customenchants;

use pocketmine\plugin\PluginBase;


class Main extends PluginBase
{
    //TODO: rewrite ce system & introduce configing

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
        $this->saveResource("max_levels.json");

        $this->getServer()->getPluginManager()->registerEvents(new EnchantListener(), $this);
        $this->getServer()->getCommandMap()->register("CustomEnchants", new EnchanterComand());

        new EnchantManager();
    }
}