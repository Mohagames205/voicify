<?php

declare(strict_types=1);

namespace mohamed205\Voicify;

use mohamed205\Voicify\socket\SocketThread;
use mohamed205\Voicify\task\SendPlayerDistanceTask;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class Voicify extends PluginBase implements Listener
{

    private static SocketThread $socketThread;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new SendPlayerDistanceTask(), 10);

        self::$socketThread = $thread = new SocketThread($this->getServer()->getLogger());
        $thread->start();
    }

    public function onJoin(PlayerJoinEvent $event)
    {

    }

    public static function getSocketThread(): SocketThread
    {
        return self::$socketThread;
    }

    public function onDisable()
    {
        self::getSocketThread()->stop();
    }

}
