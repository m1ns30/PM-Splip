<?php

declare(strict_types=1);

namespace MiniGame;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

final class EventListener implements Listener
{
    public function onLeave(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        if(MiniGame::getInstance()->isJoin($player))
        {
            MiniGame::getInstance()->removePlayer($player);
        }
    }
}