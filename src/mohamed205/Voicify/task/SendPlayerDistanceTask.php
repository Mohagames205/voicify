<?php


namespace mohamed205\Voicify\task;


use mohamed205\Voicify\math\Distance;
use mohamed205\Voicify\math\DistanceMatrix;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Internet;

class SendPlayerDistanceTask extends Task
{

    public function onRun(int $currentTick)
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            var_dump("abc");
            $distanceMatrix = new DistanceMatrix();
            foreach ($player->getLevel()->getPlayers() as $levelPlayer)
            {
                if($levelPlayer !== $player)
                {
                    $distanceMatrix->add($levelPlayer, $player->distance($levelPlayer));
                }
            }
            $distance = new Distance($player, $distanceMatrix);
            var_dump("request sent?");
            var_dump((string) $distance);
            Internet::getURL("localhost/api/distances?coordinates=" . $distance);
        }
    }

}