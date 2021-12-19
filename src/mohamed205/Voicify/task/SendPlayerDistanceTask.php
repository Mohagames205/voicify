<?php


namespace mohamed205\Voicify\task;


use mohamed205\Voicify\math\DistanceMatrix;
use mohamed205\Voicify\Voicify;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class SendPlayerDistanceTask extends Task
{

    public function onRun(): void
    {
        $distances = [];

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $distanceMatrix = new DistanceMatrix();
            foreach ($player->getWorld()->getPlayers() as $levelPlayer) {
                if ($levelPlayer !== $player) {
                    $distance = $player->getLocation()->distance($levelPlayer->getLocation());
                    $distanceMatrix->add($levelPlayer->getName(), $distance);
                }
            }
            $distances[strtolower($player->getName())] = $distanceMatrix->getDistances();
        }

        Voicify::getConnector()->tcp("update-coordinates", $distances);
    }

}