<?php

namespace pup\customenchants\items\types;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pup\customenchants\EnchantManager;
use pup\customenchants\items\CustomItem;
use pup\customenchants\items\interface\ItemInteractInterface;
use pup\customenchants\utils\Rarity;
use pup\customenchants\Main;
use Random\RandomException;

final class EnchantBook extends CustomItem implements ItemInteractInterface
{
    private int $rarity;

    public function __construct(int $rarity)
    {
        $this->rarity = $rarity;
        $item = VanillaItems::BOOK();

        $item->getNamedTag()
            ->setString(self::CUSTOM_ITEM_ID, "random_enchant_book")
            ->setInt("rarity", $rarity);

        $color = Rarity::getColor($rarity);
        $rarityName = Rarity::getName($rarity);

        parent::__construct(
            "random_enchant_book",
            $color . "Enchantment Book " . TextFormat::RESET . TextFormat::GRAY . "(" . $rarityName . ")",
            [
                TextFormat::GRAY . "Right-click to generate a random",
                TextFormat::GRAY . $rarityName . " enchantment book",
                "",
                TextFormat::DARK_GRAY . "Right-click to use"
            ],
            $item
        );
    }

    /**
     * @throws RandomException
     */
    public function onInteract(Item $item, Player $player): void
    {
        $config = Main::getInstance()->getConfig();
        $chanceEnabled = $config->get("book_chance", true);
        $baseChance = random_int(1, 100);

        $enchantments = $this->getEnchantmentsByRarity($this->rarity);

        if (empty($enchantments)) {
            $player->sendMessage(TextFormat::RED . "No enchantments available for this rarity!");
            return;
        }

        $amountToProcess = 1;
        if ($player->isSneaking()) {
            $amountToProcess = $item->getCount();
        }

        $successCount = 0;
        $itemsToGive = [];

        for ($i = 0; $i < $amountToProcess; $i++) {
            $randomEnchant = $enchantments[array_rand($enchantments)];

            $enchantmentBook = new EnchantmentBook(
                $randomEnchant,
                1);

            $itemsToGive[] = $enchantmentBook->getItem();
        }

        $result = $player->getInventory()->addItem(...$itemsToGive);

        $successCount = count($itemsToGive) - count($result);

        if ($successCount > 0) {
            $item->setCount($item->getCount() - $successCount);
            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : VanillaItems::AIR());

            if ($successCount > 1) {
                $player->sendMessage(TextFormat::GREEN . "Created {$successCount} enchantment books!");
            }
        }

        if (!empty($result)) {
            $player->sendMessage(TextFormat::RED . "Your inventory is full! Couldn't create " . count($result) . " books.");

            foreach ($result as $remainingItem) {
                $player->getWorld()->dropItem($player->getPosition(), $remainingItem);
            }
        }
    }

    private function getEnchantmentsByRarity(int $rarity): array
    {
        $enchantments = [];
        $enchantmentIds = EnchantManager::IDS;

        foreach ($enchantmentIds as $name => $id) {
            $enchant = EnchantManager::idToEnchant($id);
            if ($enchant !== null && $enchant->getRarity() === $rarity) {
                $enchantments[] = $enchant;
            }
        }

        return $enchantments;
    }
}