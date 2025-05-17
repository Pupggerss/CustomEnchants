<?php


namespace pup\customenchants\enchants\tools\pickaxe;


use pocketmine\block\Block;
use pocketmine\crafting\FurnaceType;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\world\particle\SmokeParticle;
use pocketmine\world\sound\FurnaceSound;
use pup\customenchants\types\ToolEnchant;

class AutoSmeltEnchant extends ToolEnchant
{

    public function execute(Player $player, Item $item, Block $block, ?Event $event): void {
        $furnaceManager = $player->getServer()->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::FURNACE);
        $level = $item->getEnchantment($this)->getLevel();
        $chance = $this->getChance($level, $this->getMaxLevel());

        if (random_int(1, 100) <= $chance) {
            if ($event !== null) {
                $event->setDrops(array_map(
                    function (Item $drop) use ($furnaceManager) {
                        $recipe = $furnaceManager->match($drop);
                        return $recipe ? $recipe->getResult() : $drop;
                    },
                    $event->getDrops()
                ));
            } else {
                $originalDrops = $block->getDrops($item);
                $event->setDrops(array_map(
                    function (Item $drop) use ($furnaceManager) {
                        $recipe = $furnaceManager->match($drop);
                        return $recipe ? $recipe->getResult() : $drop;
                    },
                    $originalDrops
                ));

            }

            $block->getPosition()->getWorld()->addParticle(
                $block->getPosition()->add(0.5, 0.5, 0.5),
                new SmokeParticle()
            );
            $block->getPosition()->getWorld()->addSound(
                $block->getPosition()->add(0.5, 0.5, 0.5),
                new FurnaceSound()
            );
        }
    }
}