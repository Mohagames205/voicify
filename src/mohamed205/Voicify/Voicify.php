<?php

declare(strict_types=1);

namespace mohamed205\Voicify;

use mohamed205\Voicify\socket\SocketThread;
use mohamed205\Voicify\task\SendPlayerDistanceTask;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Thread;
use pocketmine\utils\Internet;

class Voicify extends PluginBase implements Listener {

    private static SocketThread $socketThread;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new SendPlayerDistanceTask(), 10);

        self::$socketThread = $thread = new SocketThread();
        $thread->start();
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
