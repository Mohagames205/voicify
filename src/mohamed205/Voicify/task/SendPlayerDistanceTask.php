<?php


namespace mohamed205\Voicify\task;


use mohamed205\Voicify\math\DistanceMatrix;
use mohamed205\Voicify\Voicify;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class SendPlayerDistanceTask extends Task
{

    public function onRun(int $currentTick)
    {
        $distances = [];

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $distanceMatrix = new DistanceMatrix();
            foreach ($player->getLevel()->getPlayers() as $levelPlayer) {
                if ($levelPlayer !== $player) {
                    $distanceMatrix->add($levelPlayer->getName(), $player->distance($levelPlayer));
                }
            }
            $distances[strtolower($player->getName())] = $distanceMatrix->getDistances();
        }

        Voicify::getConnector()->tcp("update-coordinates", $distances);
    }

}