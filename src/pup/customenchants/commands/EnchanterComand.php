<?php


namespace pup\customenchants\commands;


use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pup\customenchants\commands\sub\AddEnchantSub;
use pup\customenchants\commands\sub\RemoveEnchantSub;
use pup\customenchants\Main;

final class EnchanterComand extends BaseCommand
{
    //TODO: REDO AND MAKE THIS BETTER LMAO
    public function __construct(private readonly Main $main)
    {
        parent::__construct($this->main,"enchanter", "Adds an enchant to the item in hand", ["ce"]);
        $this->setPermission("enchant.command");
    }

    protected function prepare()
    : void
    {
        $this->addConstraint(new InGameRequiredConstraint($this));

        $this->registerSubCommand(new AddEnchantSub($this->main));
        $this->registerSubCommand(new RemoveEnchantSub($this->main));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args)
    : void
    {
        // TODO: Implement onRun() method.
    }
}