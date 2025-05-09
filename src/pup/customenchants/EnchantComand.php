<?php


namespace pup\customenchants;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\player\Player;

class EnchantComand extends Command
{
    public function __construct()
    {
        parent::__construct("enchanter", "Adds an enchant to the item in hand", "/enchanter", ["ce"]);
        $this->setPermission("enchanter.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            $enchant = $args[0];
            if (is_numeric($enchant)) {
                $flip = array_flip(EnchantManager::ID);
                if (!isset($flip[$enchant])) {
                    $sender->sendMessage("There is no enchant with that id.");
                    return;
                }
            } else {
                if (!isset(EnchantManager::ID[$enchant])) {
                    $sender->sendMessage("There is no enchant with that name.");
                    return;
                }
                $enchant = EnchantManager::ID[$enchant];
            }
            $enchant = EnchantmentIdMap::getInstance()->fromId($enchant);
            $item = $sender->getInventory()->getItemInHand();
            if (!$item instanceof Durable) {
                $sender->sendMessage("This item cannot be enchanted");
                return;
            }
            $level = $args[1];
            if (is_nan($level)) {
                $sender->sendMessage("The level must be a number.");
                return;
            }
            $level = abs($level);
            $item->addEnchantment(new EnchantmentInstance($enchant, $level));
            $item = EnchantManager::loreItem($item);
            $sender->getInventory()->setItemInHand($item);
            $sender->sendMessage("Item successfully enchanted.");
        }
    }
}