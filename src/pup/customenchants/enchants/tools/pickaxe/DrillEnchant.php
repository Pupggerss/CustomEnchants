<?php


namespace pup\customenchants\enchants\tools\pickaxe;


use pocketmine\block\Block;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\math\Axis;
use pocketmine\math\Facing;
use pocketmine\player\Player;
use pup\customenchants\types\ToolEnchant;

class DrillEnchant extends ToolEnchant
{
    private static array $lastBreakFace = [];
    private bool $breakingBlocks = false;

    public function execute(Player $player, Item $item, Block $block, ?Event $event)
    : void
    {
        if ($this->breakingBlocks) {
            return;
        }

        $level = $item->getEnchantment($this)->getLevel();
        $chance = $this->getChance($level, $this->getMaxLevel());

        if (random_int(1, 100) <= $chance) {
            $breakFace = self::$lastBreakFace[$player->getName()] ?? Facing::UP;

            for ($i = 0; $i <= min($level - 1, 1); $i++) {
                $currentBlock = $block->getSide(Facing::opposite($breakFace), $i);

                $faceLeft = Facing::rotate($breakFace, Facing::axis($breakFace) !== Axis::Y ? Axis::Y : Axis::X, true);
                $faceUp = Facing::rotate($breakFace, Facing::axis($breakFace) !== Axis::Z ? Axis::Z : Axis::X, true);

                $blocksToBreak = [
                    $currentBlock->getSide($faceLeft),
                    // Center Left
                    $currentBlock->getSide(Facing::opposite($faceLeft)),
                    // Center Right
                    $currentBlock->getSide($faceUp),
                    // Center Top
                    $currentBlock->getSide(Facing::opposite($faceUp)),
                    // Center Bottom
                    $currentBlock->getSide($faceUp)->getSide($faceLeft),
                    // Top Left
                    $currentBlock->getSide($faceUp)->getSide(Facing::opposite($faceLeft)),
                    // Top Right
                    $currentBlock->getSide(Facing::opposite($faceUp))->getSide($faceLeft),
                    // Bottom Left
                    $currentBlock->getSide(Facing::opposite($faceUp))->getSide(Facing::opposite($faceLeft))
                ];

                foreach ($blocksToBreak as $b) {
                    $this->breakingBlocks = true;
                    if($b->getBreakInfo()->isToolCompatible($item)) {
                        $player->getWorld()->useBreakOn($b->getPosition(), $item, $player);
                    }
                }

                if (!$block->getPosition()->equals($currentBlock->getPosition())) {
                    $this->breakingBlocks = true;
                    $player->getWorld()->useBreakOn($currentBlock->getPosition(), $item, $player);
                }
            }
            $this->breakingBlocks = false;
        }
    }
}