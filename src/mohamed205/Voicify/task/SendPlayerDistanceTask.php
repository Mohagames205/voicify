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
        $distances = [];

        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $distanceMatrix = new DistanceMatrix();
            foreach ($player->getLevel()->getPlayers() as $levelPlayer)
            {
                if($levelPlayer !== $player)
                {
                    $distanceMatrix->add($levelPlayer->getName(), $player->distance($levelPlayer));
                }
            }
            $distance[] = new Distance($player->getName(), $distanceMatrix);
        }

        Server::getInstance()->getAsyncPool()->submitTask(new class($distances) extends AsyncTask {

            private $distances;

            public function __construct($distances)
            {
                $this->distances = $distances;
            }
            public function onRun()
            {
                Internet::postURL("https://voicify-web.herokuapp.com/api/distances", "coordinates=" . $this->distances . "&roomId=mo");
            }
        });
    }

}