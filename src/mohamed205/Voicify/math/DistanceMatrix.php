<?php


namespace mohamed205\Voicify\math;


use pocketmine\Player;

class DistanceMatrix
{

    private array $distances = [];

    public function add(Player $player, int $distance)
    {
        $this->distances[strtolower($player->getName())] = $distance;
    }

    public function getDistance(Player $player) : ?int
    {
        return $this->distances[strtolower($player->getPlayer())] ?? null;
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString()
    {
        $arrayify = [];
        foreach ($this->distances as $player => $distance)
        {
            $arrayify[strtolower($player->getName())] = $distance;
        }
        return json_encode($arrayify);
    }

}