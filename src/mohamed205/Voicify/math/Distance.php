<?php


namespace mohamed205\Voicify\math;


use pocketmine\Player;

class Distance
{

    private Player $primaryPlayer;

    private DistanceMatrix $distanceMatrix;

    public function __construct(Player $primaryPlayer, DistanceMatrix $distanceMatrix)
    {
        $this->primaryPlayer = $primaryPlayer;
        $this->distanceMatrix = $distanceMatrix;
    }

    public function __toString()
    {
        return json_encode([strtolower($this->primaryPlayer->getName()) => $this->distanceMatrix->toString()]);
    }

}