<?php

declare(strict_types=1);

namespace MiniGame\form;

use MiniGame\MiniGame;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function strtolower;

final class MiniGameForm implements Form
{
    public function __construct(private Player $player) {
    }

    public function jsonSerialize(): array
    {
        $buttons = [];
        if(MiniGame::getInstance()->isJoin($this->player))
        {
            $buttons[] = MiniGame::makeButton('나가기', '게임에서 나갑니다');
        }else{
            $buttons[] = MiniGame::makeButton('참여하기', '게임에 참여합니다');
        }
        return [
            'type' => 'form',
            'title' => '§l§8MINI GAME',
            'content' => MiniGame::FormPrefix . '원하는 기능을 골라주세요',
            'buttons' => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        if($data===0)
        {
            if(!MiniGame::getInstance()->isReadyTime())
            {
                $player->sendMessage(MiniGame::ChatPrefix . '준비시간에만 가능합니다');
                return;
            }
            if(MiniGame::getInstance()->isJoin($this->player))
            {
                MiniGame::getInstance()->removePlayer($player);
                $player->getServer()->broadcastMessage(MiniGame::ChatPrefix . strtolower($player->getName()) . '님이 나갔습니다 §7' . count(MiniGame::getInstance()->getPlayers()) . '명');
            }else{
                MiniGame::getInstance()->addPlayer($player);
                $player->getServer()->broadcastMessage(MiniGame::ChatPrefix . strtolower($player->getName()) . '님이 들어왔습니다 §7' . count(MiniGame::getInstance()->getPlayers()) . '명');
            }
        }
    }
}