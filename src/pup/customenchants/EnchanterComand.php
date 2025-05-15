<?php


namespace pup\customenchants;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EnchanterComand extends Command
{
    //TODO: REDO AND MAKE THIS BETTER LMAO
    public function __construct()
    {
        parent::__construct("enchanter", "Adds an enchant to the item in hand", "/enchanter", ["ce"]);
        $this->setPermission("enchanter.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if(count($args) < 2){
                $sender->sendMessage(TextFormat::RED . "Usage: /enchanter <enchant name/id> <enchantlevel>");
                return;
            }
            $enchant = $args[0];
            if (is_numeric($enchant)) {
                $flip = array_flip(EnchantManager::IDS);
                if (!isset($flip[$enchant])) {
                    $sender->sendMessage(TextFormat::RED . "There is no enchant with that id.");
                    return;
                }
            } else {
                if (!isset(EnchantManager::IDS[$enchant])) {
                    $sender->sendMessage(TextFormat::RED . "There is no enchant with that name.");
                    return;
                }
                $enchant = EnchantManager::IDS[$enchant];
            }
            $enchant = EnchantmentIdMap::getInstance()->fromId($enchant);
            $item = $sender->getInventory()->getItemInHand();

            if($item === VanillaItems::AIR() || !$item instanceof Item){
                $sender->sendMessage(TextFormat::RED . "No item in hand!");
                return;
            }

            if (!$item instanceof Durable) {
                $sender->sendMessage("This item cannot be enchanted");
                return;
            }

            $level = $args[1];
            if (is_nan($level)) {
                $sender->sendMessage(TextFormat::RED . "The level must be a number.");
                return;
            }

            $level = abs($level);
            //TODO: Check item and if level greater than max

            $item->addEnchantment(new EnchantmentInstance($enchant, $level));
            $item = EnchantManager::loreItem($item);
            $sender->getInventory()->setItemInHand($item);
            $sender->sendMessage(TextFormat::GREEN . "Item successfully enchanted.");
        }
    }
}