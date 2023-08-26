<?php

declare(strict_types=1);

namespace MiniGame;

use MiniGame\command\MiniAdminCommand;
use MiniGame\command\MiniCommand;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskHandler;
use pocketmine\world\Position;
use pocketmine\utils\Config;
use function strtolower;

final class MiniGame extends PluginBase
{
    private Config $data;

    public array $db;

    private static ?self $instance = null;

    public ?TaskHandler $task = null;

    public const FormPrefix = '§r§l§a▶ §r§f';

    public const ChatPrefix = '§r§l§a[미니게임] §r§f';

    public static function makeButton(string $title, string $subtitle): array
    {
        return ['text' => "§l$title\n§r§8▶ $subtitle §r§8◀"];
    }

    public static function convertName(Player|string $player): string
    {
        return $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    protected function onLoad(): void
    {
        self::$instance = $this;
    }

    protected function onEnable(): void
    {
        $server = $this->getServer();
        $server->getCommandMap()->registerAll($this->getName(), [new MiniCommand(), new MiniAdminCommand()]);
        $server->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->data = new Config($this->getDataFolder() . 'data.json', Config::JSON, [
            'state' => 'off',
            'game' => 'world:0:0:0',
            'players' => []
        ]);
        $this->db = $this->data->getAll();
    }

    protected function onDisable(): void
    {
        $this->endGame();
        $this->data->setAll($this->db);
        $this->data->save();
    }

    public function isStartTime(): bool
    {
        return $this->db['state'] === 'on';
    }

    public function isReadyTime(): bool
    {
        return $this->db['state'] === 'ready';
    }

    public function setState(string $state): void
    {
        $this->db['state'] = $state;
    }

    public function isJoin(Player|string $player): bool
    {
        return in_array(strtolower($player->getName()), array_keys($this->db['players']));
    }

    public function getPlayers(): array
    {
        return $this->db['players'];
    }

    public function getPlayersKey(): array
    {
        return array_keys($this->db['players']);
    }

    public function positionHash(Position $pos): string
    {
        return $pos->getWorld()->getFolderName() . ':' . $pos->getX() . ':' . $pos->getY() . ':' . $pos->getZ();
    }

    public function getPosition(string $position): Position
    {
        $position = explode(':', $position);
        return new Position((float)$position[1], (float)$position[2], (float)$position[3], $this->getServer()->getWorldManager()->getWorldByName($position[0]));
    }

    public function start(): void
    {
        $this->getServer()->broadcastMessage(MiniGame::ChatPrefix . '스플리프이 시작됐습니다');
        $this->setState('on');
        $this->task?->cancel();
        foreach ($this->getPlayers() as $name => $value) {
            if (($player = $this->getServer()->getPlayerByPrefix($name)) instanceof Player) {
                $player->teleport($this->getPosition($this->db['game']));
                $player->getInventory()->addItem(VanillaItems::DIAMOND_SHOVEL());
            }
        }
    }

    public function ready(): void
    {
        $this->getServer()->broadcastMessage('');
        $this->getServer()->broadcastMessage(MiniGame::ChatPrefix . '스플리프 시작전입니다.');
        $this->getServer()->broadcastMessage('/스플리프 으로 참가하세요.');
        $this->getServer()->broadcastMessage('');
        $this->setState('ready');
        foreach ($this->getPlayers() as $name => $value) {
            if (($player = $this->getServer()->getPlayerByPrefix($name)) instanceof Player) {

                $player->teleport($this->getPosition($this->db['game']));
            }
        }
    }

    public function endGame(): void
    {
        $this->getServer()->broadcastMessage(MiniGame::ChatPrefix . '스플리프이 끝났습니다');
        $this->setState('off');
        $this->numberrank = 1;
        $this->task?->cancel();
        foreach ($this->getPlayers() as $name => $value) {
            $this->removePlayer($name);
            if (($player = $this->getServer()->getPlayerByPrefix($name)) instanceof Player) {
                $player->teleport($this->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                $player->kick('참여해주셔서 감사합니다' . "\n" . '데이터를 초기화 하기 위해 다시 접속해주세요');
            }
        }
        $this->db['players'] = [];
    }

    public function addPlayer(Player|string $player): void
    {
        $this->db['players'][self::convertName($player)] = 0;
    }

    public function removePlayer(Player|string $player): void
    {
        unset($this->db['players'][self::convertName($player)]);
    }
}