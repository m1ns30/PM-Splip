<?php

declare(strict_types=1);

namespace MiniGame\form;

use MiniGame\MiniGame;
use pocketmine\form\Form;
use pocketmine\player\Player;

final class MiniGameAdminForm implements Form
{
    public function jsonSerialize(): array
    {
        return [
            'type' => 'form',
            'title' => '§l§8MINI GAME',
            'content' => MiniGame::FormPrefix . '기능을 선택해주세요',
            'buttons' => [
                MiniGame::makeButton('끄기', '점령전 상태 끔으로 바꿉니다'),
                MiniGame::makeButton('준비', '점령전 상태 준비로 바꿉니다'),
                MiniGame::makeButton('시작', '점령전 상태 시작으로 바꿉니다'),
                MiniGame::makeButton('게임장소설정', '게임장소설정'),
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data===0)
        {
            MiniGame::getInstance()->endGame();
            return;
        }
        if($data===1)
        {
            MiniGame::getInstance()->ready();
            return;
        }
        if($data===2)
        {
            MiniGame::getInstance()->start();
            return;
        }
        if($data===3)
        {
            MiniGame::getInstance()->db['game'] = MiniGame::getInstance()->positionHash($player->getPosition());
            $player->sendMessage(MiniGame::getInstance()->positionHash($player->getPosition()) . '으로 설정했습니다');
        }
    }
}