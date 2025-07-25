<?php


namespace pup\customenchants;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pup\customenchants\commands\EnchanterComand;
use pup\customenchants\items\ItemListener;
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

    /**
     * @throws HookAlreadyRegistered
     */
    public function onEnable()
    : void
    {
        if(!PacketHooker::isRegistered()){
            PacketHooker::register($this);
        }

        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }

        $this->saveResource("enchantments.json");
        $this->saveDefaultConfig();
        Rarity::init($this->getConfig()->get("rarities"));

        $this->getServer()->getPluginManager()->registerEvents(new EnchantListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new ItemListener(), $this);
        $this->getServer()->getCommandMap()->register("CustomEnchants", new EnchanterComand($this));

        new EnchantManager();
    }
}