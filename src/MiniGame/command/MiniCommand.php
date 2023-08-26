<?php

declare(strict_types=1);

namespace MiniGame\command;

use MiniGame\form\MiniGameForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class MiniCommand extends Command
{
    public function __construct()
    {
        parent::__construct('스플리프', '스플리프 관련 명령어 입니다');
        $this->setPermission('minigame.user');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(!$this->testPermission($sender)) return;
        if(!$sender instanceof Player) return;
        $sender->sendForm(new MiniGameForm($sender));
    }
}