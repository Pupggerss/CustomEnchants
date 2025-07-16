<?php

namespace pup\customenchants\commands\sub;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pup\customenchants\EnchantManager;
use pup\customenchants\Main;

final class RemoveEnchantSub extends BaseSubCommand
{

    public function __construct(private readonly Main $main)
    {
        parent::__construct($this->main, "remove", "Removes an enchantment");
        $this->setPermission("enchant.command.remove");
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare()
    : void
    {
        $this->registerArgument(0, new RawStringArgument('enchant'));
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

            $item->removeEnchantment($enchant);
            $item = EnchantManager::loreItem($item);
            $sender->getInventory()->setItemInHand($item);
            $sender->sendMessage(TextFormat::GREEN . "Successfully removed " . $enchant->getName() . "!");
        }
    }
}