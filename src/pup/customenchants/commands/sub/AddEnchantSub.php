<?php

namespace pup\customenchants\commands\sub;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pup\customenchants\EnchantManager;
use pup\customenchants\Main;

final class AddEnchantSub extends BaseSubCommand
{
    public function __construct(private readonly Main $main)
    {
        parent::__construct($this->main, "add", "Add a new enchantment");
        $this->setPermission("enchant.command.add");
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare()
    : void
    {
        $this->registerArgument(0, new RawStringArgument('enchant'));
        $this->registerArgument(1, new IntegerArgument('level', true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args)
    : void
    {
        if(!$sender instanceof Player) return;

        if(isset($args['enchant'])) {
            if(!isset(EnchantManager::IDS[$args['enchant']])) {
                $sender->sendMessage(TextFormat::RED . "Invalid enchantment name.");
            }
            $enchant  = EnchantManager::IDS[$args['enchant']];

            $enchant = EnchantmentIdMap::getInstance()->fromId($enchant);

            if(!$enchant instanceof Enchantment){
                $sender->sendMessage(TextFormat::RED . "Invalid enchantment.");
                return;
            }

            $item = $sender->getInventory()->getItemInHand();

            if($item->isNull()){
                $sender->sendMessage(TextFormat::RED . "Item is null.");
                return;
            }

            $level = $args['level'];

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
}