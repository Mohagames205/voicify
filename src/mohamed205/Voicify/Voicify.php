<?php

declare(strict_types=1);

namespace mohamed205\Voicify;

use mohamed205\Voicify\task\SendPlayerDistanceTask;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Internet;

class Voicify extends PluginBase implements Listener {

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new SendPlayerDistanceTask(), 30);
    }

}
