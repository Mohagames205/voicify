<?php


namespace Mohamed205\voxum\task;


use Mohamed205\voxum\math\DistanceMatrix;
use Mohamed205\voxum\Voicify;
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