<?php


namespace mohamed205\Voicify\task;


use mohamed205\Voicify\math\Distance;
use mohamed205\Voicify\math\DistanceMatrix;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Internet;

class SendPlayerDistanceTask extends Task
{

    public function onRun(int $currentTick)
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $distanceMatrix = new DistanceMatrix();
            foreach ($player->getLevel()->getPlayers() as $levelPlayer)
            {
                if($levelPlayer !== $player)
                {
                    $distanceMatrix->add($levelPlayer->getName(), $player->distance($levelPlayer));
                }
            }
            $distance = new Distance($player->getName(), $distanceMatrix);
            var_dump((string) $distance);

            Server::getInstance()->getAsyncPool()->submitTask(new class($distance) extends AsyncTask {

                private $distance;

                public function __construct($distance)
                {
                    $this->distance = $distance;
                }
                public function onRun()
                {
                    Internet::postURL("localhost/api/distances", "coordinates=" . $this->distance);
                }
            });

        }
    }

}