<?php

namespace pup\customenchants\items\types;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\AnvilUseSound;
use pup\customenchants\CustomEnchant;
use pup\customenchants\EnchantManager;
use pup\customenchants\items\CustomItem;
use pup\customenchants\items\interface\InventoryTransactionInterface;
use pup\customenchants\utils\Rarity;

final class EnchantmentBook extends CustomItem implements InventoryTransactionInterface
{
    public function __construct(Enchantment $enchantment, int $level = 1){
        $item = VanillaItems::BOOK();

        $item->getNamedTag()
            ->setString(self::CUSTOM_ITEM_ID, "enchantment_book")
            ->setString("enchant", $enchantment->getName())
            ->setInt("level", $level)
            ->setString("uid", uniqid());

        if($enchantment instanceof CustomEnchant){
            $color = Rarity::getColor($enchantment->getRarity());
            $name = $color . $enchantment->getName();
        } else {
            $name = $enchantment->getName();
        }

        parent::__construct(
            "enchantment_book",
            $name . " " . $level,
            [
                "Right-click on an item to enchant it",
                "Combine two books to increase the level of an enchantment",
                "",
                TextFormat::DARK_GRAY . "Right-click on an item to use",
                "",
                Rarity::getColor($enchantment->getRarity()) . "Rarity: " . Rarity::getName($enchantment->getRarity())
            ],
            $item
        );
    }

    public function onInventoryTransaction(InventoryTransactionEvent $e, Player $player, Item $item, Item $target, SlotChangeAction $action1, SlotChangeAction $action2)
    : void
    {
        $book = null;
        $targetItem = null;
        $bookAction = null;
        $targetAction = null;

        if ($this->isEnchantBook($item)) {
            $book = $item;
            $targetItem = $target;
            $bookAction = $action1;
            $targetAction = $action2;
        } elseif ($this->isEnchantBook($target)) {
            $book = $target;
            $targetItem = $item;
            $bookAction = $action2;
            $targetAction = $action1;
        }

        if ($book === null || $targetItem === null || $targetItem->isNull()) {
            return;
        }

        $bookEnchantName = $book->getNamedTag()->getString("enchant");
        $bookLevel = $book->getNamedTag()->getInt("level");

        if ($this->isEnchantBook($targetItem)) {
            $targetEnchantName = $targetItem->getNamedTag()->getString("enchant");
            $targetLevel = $targetItem->getNamedTag()->getInt("level");

            if ($bookEnchantName === $targetEnchantName && $bookLevel === $targetLevel) {
                $maxLevel = EnchantManager::idToEnchant(EnchantManager::nameToId($bookEnchantName))?->getMaxLevel() ?? 1;

                if ($bookLevel < $maxLevel) {
                    $newLevel = $bookLevel + 1;

                    $newBook = new self(EnchantManager::idToEnchant(EnchantManager::nameToId($bookEnchantName)), $newLevel);

                    $book->pop();
                    $targetItem->pop();

                    $targetAction->getInventory()->setItem($targetAction->getSlot(), $newBook->getItem());
                    $bookAction->getInventory()->setItem($bookAction->getSlot(), VanillaItems::AIR());

                    $player->getWorld()->addSound($player->getLocation(), new AnvilUseSound());
                } else {
                    $player->sendMessage(TextFormat::RED . "This enchantment is already at its maximum level!");
                    $player->getWorld()->addSound($player->getLocation(), new AnvilFallSound());
                }
            } else {
                $player->sendMessage(TextFormat::RED . "You can only combine books with the same enchantment and level!");
                $player->getWorld()->addSound($player->getLocation(), new AnvilFallSound());
            }
            $e->cancel();
            return;
        }

        if (!$targetItem instanceof Durable) {
            $player->sendMessage(TextFormat::RED . "You can only enchant durable items!");
            $player->getWorld()->addSound($player->getLocation(), new AnvilFallSound());
            $e->cancel();
            return;
        }

        if (EnchantManager::canApplyEnchant($bookEnchantName, $targetItem)) {
            $enchant = EnchantManager::idToEnchant(EnchantManager::nameToId($bookEnchantName));
            if ($enchant !== null) {
                $existingEnchant = $targetItem->getEnchantment($enchant);
                if ($existingEnchant !== null) {
                    $maxLevel = $enchant->getMaxLevel();
                    if ($existingEnchant->getLevel() >= $bookLevel) {
                        $player->sendMessage(TextFormat::RED . "The item already has this enchantment at an equal or higher level!");
                        $player->getWorld()->addSound($player->getLocation(), new AnvilFallSound());
                        $e->cancel();
                        return;
                    }
                    if ($existingEnchant->getLevel() >= $maxLevel) {
                        $player->getWorld()->addSound($player->getLocation(), new AnvilFallSound());
                        $player->sendMessage(TextFormat::RED . "This enchantment is already at its maximum level on this item!");
                        $e->cancel();
                        return;
                    }
                }

                $targetItem->addEnchantment(new EnchantmentInstance($enchant, $bookLevel));
                EnchantManager::loreItem($targetItem);

                $book->pop();
                $bookAction->getInventory()->setItem($bookAction->getSlot(), $book->getCount() > 0 ? $book : VanillaItems::AIR());
                $targetAction->getInventory()->setItem($targetAction->getSlot(), $targetItem);

                $player->getWorld()->addSound($player->getLocation(), new AnvilUseSound());
                $e->cancel();
                return;
            }
        }

        $player->sendMessage(TextFormat::RED . "This enchantment cannot be applied to this item!");
        $e->cancel();
    }

    private function isEnchantBook(Item $item): bool
    {
        return $item->getNamedTag()->getString(self::CUSTOM_ITEM_ID, "") === "enchantment_book";
    }
}