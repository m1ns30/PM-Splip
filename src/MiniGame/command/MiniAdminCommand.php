<?php

declare(strict_types=1);

namespace MiniGame\command;

use MiniGame\form\MiniGameAdminForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class MiniAdminCommand extends Command
{
    public function __construct()
    {
        parent::__construct('스플리프관리', '스플리프관리 관련 명령어 입니다');
        $this->setPermission('minigame.op');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(!$this->testPermission($sender)) return;
        if(!$sender instanceof Player) return;
        $sender->sendForm(new MiniGameAdminForm());
    }
}