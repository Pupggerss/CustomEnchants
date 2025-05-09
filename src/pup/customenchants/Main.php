<?php


namespace pup\customenchants;

use pocketmine\command\defaults\EnchantCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;


class Main extends PluginBase
{

    private static self $instance;
    private Config $config;

    public function getInstance(): Main{
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
        $this->config = new Config($this->getDataFolder() . "max_levels.json", Config::JSON);
        $this->config->save();

        $this->getServer()->getPluginManager()->registerEvents(new EnchantListener(), $this);
        $this->getServer()->getCommandMap()->register($this, new EnchantCommand());

        new EnchantManager($this->config);
    }
}