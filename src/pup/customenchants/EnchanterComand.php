<?php


namespace pup\customenchants;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
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
    : void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return;
        }

        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::RED . "Usage: /enchanter <enchantName|enchantID> <level>");
            $sender->sendMessage(TextFormat::GOLD . "Available enchants: " . implode(", ", array_keys(EnchantManager::IDS)));
            return;
        }

        $enchantInput = strtolower($args[0]);

        if (is_numeric($enchantInput)) {
            $enchantId = (int)$enchantInput;
            if (!in_array($enchantId, EnchantManager::IDS, true)) {
                $sender->sendMessage(TextFormat::RED . "Invalid enchantment ID.");
                return;
            }
        } else {
            if (!isset(EnchantManager::IDS[$enchantInput])) {
                $sender->sendMessage(TextFormat::RED . "Invalid enchantment name.");
                return;
            }
            $enchantId = EnchantManager::IDS[$enchantInput];
        }

        $enchant = EnchantmentIdMap::getInstance()->fromId($enchantId);
        if (!$enchant instanceof Enchantment) {
            $sender->sendMessage(TextFormat::RED . "Failed to get enchantment. Contact admin.");
            return;
        }

        $item = $sender->getInventory()->getItemInHand();
        if ($item->isNull()) {
            $sender->sendMessage(TextFormat::RED . "No item in hand!");
            return;
        }

        // Validate level
        $level = $args[1];
        if (!is_numeric($level)) {
            $sender->sendMessage(TextFormat::RED . "The level must be a number.");
            return;
        }

        $level = (int)abs($level);
        $config = Main::getInstance()->getConfig();

        if ($config->get("max_level", true) && $level > $enchant->getMaxLevel()) {
            $sender->sendMessage(TextFormat::RED . "Level exceeds max level ({$enchant->getMaxLevel()})!");
            return;
        }

        if (!EnchantManager::canApplyEnchant($enchant->getName(), $item)) {
            $sender->sendMessage(TextFormat::RED . "This enchant doesn't work on this item!");
            return;
        }

        $item->addEnchantment(new EnchantmentInstance($enchant, $level));
        $item = EnchantManager::loreItem($item);
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage(TextFormat::GREEN . "Successfully applied " . $enchant->getName() . " " . $level . "!");
    }
}