<?php


namespace pup\customenchants\types\enchants\sword;


use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\enchantment\MeleeWeaponEnchantment;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pup\customenchants\types\ChanceTriat;

class ZuesEnchant extends MeleeWeaponEnchantment
{
    use ChanceTriat;

    /**
     * @inheritDoc
     */
    public function isApplicableTo(Entity $victim): bool
    {
        return $victim instanceof Living;
    }

    /**
     * @inheritDoc
     */
    public function getDamageBonus(int $enchantmentLevel): float
    {
        return 0;
    }

    public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel): void
    {
        if ($victim instanceof Player && $attacker instanceof Player) {
            $chance = $this->calculateChance($enchantmentLevel, $this->getMaxLevel(), 5);
            if(random_int(1, 100) <= $chance) {
                self::lightning($victim);
                $victim->setHealth($victim->getHealth() - ($enchantmentLevel * 2));
            }
        }
    }

    private static function lightning(Entity $player): void
    {
        $pos = $player->getPosition();
        $light2 = new AddActorPacket();
        $light2->actorUniqueId = Entity::nextRuntimeId();
        $light2->actorRuntimeId = 1;
        $light2->position = $player->getPosition()->asVector3();
        $light2->type = "minecraft:lightning_bolt";
        $light2->yaw = $player->getLocation()->getYaw();
        $light2->syncedProperties = new PropertySyncData([], []);

        $block = $player->getWorld()->getBlock($player->getPosition()->floor()->down());
        $particle = new BlockBreakParticle($block);

        $player->getWorld()->addParticle($pos, $particle, $player->getWorld()->getPlayers());
        $sound2 = PlaySoundPacket::create("ambient.weather.thunder", $pos->getX(), $pos->getY(), $pos->getZ(), 1, 1);

        NetworkBroadcastUtils::broadcastPackets($player->getWorld()->getPlayers(), [$light2, $sound2]);
    }

}