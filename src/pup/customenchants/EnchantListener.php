<?php


namespace pup\customenchants;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pup\customenchants\items\ItemRegistry;
use pup\customenchants\items\types\EnchantmentBook;
use pup\customenchants\types\BowEnchant;
use pup\customenchants\types\RandomArmorEnchant;
use pup\customenchants\types\ToggledArmorEnchant;
use pup\customenchants\types\ToolEnchant;

class EnchantListener implements Listener
{
    /**
     * @param InventoryTransactionEvent $event
     * @priority HIGHEST
     */
    public function onArmorChange(InventoryTransactionEvent $event)
    : void
    {
        ToggledArmorEnchant::onToggle($event);
    }

    /**
     * @param EntityDamageEvent $event
     * @priority HIGHEST
     */
    public function onDamage(EntityDamageEvent $event)
    : void
    {
        RandomArmorEnchant::onDamage($event);
    }

    /**
     * @param BlockBreakEvent $event
     * @priority HIGHEST
     */
    public function onBreak(BlockBreakEvent $event)
    : void
    {
        if ($event->isCancelled()) {
            return;
        }
        ToolEnchant::onBreak($event);
    }

    /**
     * @param ProjectileHitBlockEvent $event
     * @priority HIGHEST
     */
    public function onBlockHit(ProjectileHitBlockEvent $event)
    : void
    {
        BowEnchant::onHitBlock($event);
    }

    /**
     * @param PlayerJoinEvent $event
     * @priority HIGHEST
     */

    public function onJoin(PlayerJoinEvent $event)
    : void
    {
        $player = $event->getPlayer();
        foreach ($player->getArmorInventory()->getContents() as $item) {
            foreach ($item->getEnchantments() as $enchantment) {
                $enchantment = $enchantment->getType();
                if ($enchantment instanceof ToggledArmorEnchant) {
                    $enchantment->onEquip($player, $item);
                }
            }
        }
    }

    /**
     * @param PlayerDeathEvent $event
     * @priority HIGHEST
     */
    public function onDeath(PlayerDeathEvent $event)
    : void
    {
        $player = $event->getPlayer();
        foreach ($player->getArmorInventory()->getContents() as $item) {
            foreach ($item->getEnchantments() as $enchantment) {
                $enchantment = $enchantment->getType();
                if ($enchantment instanceof ToggledArmorEnchant) {
                    $enchantment->onDequip($player, $item);
                    ToggledArmorEnchant::removeArmorListener($player);
                }
            }
        }
    }

    /**
     * @param PlayerQuitEvent $event
     * @priority HIGHEST
     */
    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        ToggledArmorEnchant::removeArmorListener($player);
    }
}