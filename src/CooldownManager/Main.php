<?php

namespace CooldownManager;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

    private array $pearlCooldown = [];
    private array $gappleCooldown = [];

    public function onEnable(): void{
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onUse(PlayerItemUseEvent $event): void{
        $player = $event->getPlayer();

        if($player->hasPermission("cooldown.bypass")){
            return;
        }

        $item = $event->getItem();
        $time = time();

        $pearlCooldownTime = $this->getConfig()->get("cooldowns")["ender_pearl"];
        $gappleCooldownTime = $this->getConfig()->get("cooldowns")["enchanted_golden_apple"];

        // ENDER PEARL
        if($item->getTypeId() === VanillaItems::ENDER_PEARL()->getTypeId()){

            if(isset($this->pearlCooldown[$player->getName()])){
                $remaining = $this->pearlCooldown[$player->getName()] - $time;

                if($remaining > 0){
                    $msg = str_replace("{time}", $remaining, $this->getConfig()->get("messages")["ender_pearl"]);
                    $player->sendMessage($msg);
                    $event->cancel();
                    return;
                }
            }

            $this->pearlCooldown[$player->getName()] = $time + $pearlCooldownTime;
        }

        // ENCHANTED GOLDEN APPLE
        if($item->getTypeId() === VanillaItems::ENCHANTED_GOLDEN_APPLE()->getTypeId()){

            if(isset($this->gappleCooldown[$player->getName()])){
                $remaining = $this->gappleCooldown[$player->getName()] - $time;

                if($remaining > 0){
                    $msg = str_replace("{time}", $remaining, $this->getConfig()->get("messages")["enchanted_golden_apple"]);
                    $player->sendMessage($msg);
                    $event->cancel();
                    return;
                }
            }

            $this->gappleCooldown[$player->getName()] = $time + $gappleCooldownTime;
        }
    }
}
