<?php


namespace pup\customenchants;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EnchanterComand extends Command
{
    public function __construct()
    {
        parent::__construct("enchanter", "Adds an enchant to the item in hand", "/enchanter", ["ce"]);
        $this->setPermission("enchanter.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return;
        }

        if(count($args) < 2) {
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
            $enchant = $flip[$enchant];
        } else {
            if (!isset(EnchantManager::IDS[$enchant])) {
                $sender->sendMessage(TextFormat::RED . "There is no enchant with that name.");
                return;
            }
            $enchant = EnchantManager::IDS[$enchant];
        }

        $enchant = EnchantmentIdMap::getInstance()->fromId($enchant);
        if(!$enchant instanceof Enchantment) {
            $sender->sendMessage(TextFormat::RED . "An error occurred");
            return;
        }

        $item = $sender->getInventory()->getItemInHand();
        if($item->isNull()) {
            $sender->sendMessage(TextFormat::RED . "No item in hand!");
            return;
        }

        $level = $args[1];
        if (!is_numeric($level)) {
            $sender->sendMessage(TextFormat::RED . "The level must be a number.");
            return;
        }

        $level = (int)abs($level);

        $config = Main::getInstance()->getConfig();
        $maxlevel = $config->get("max_level", true);
        if($maxlevel) {
            if ($level > $enchant->getMaxLevel()) {
                $sender->sendMessage(TextFormat::RED . "Provided level is greater than max level!");
                return;
            }
        }
        $check = EnchantManager::canApplyEnchant($enchant->getName(), $item);

        if(!$check) {
            $sender->sendMessage(TextFormat::RED . "This enchant doesn't work on this item!");
            return;
        }

        $item->addEnchantment(new EnchantmentInstance($enchant, $level));
        $item = EnchantManager::loreItem($item);
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage(TextFormat::GREEN . "Item successfully enchanted.");
    }}