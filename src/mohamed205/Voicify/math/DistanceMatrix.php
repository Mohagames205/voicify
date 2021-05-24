<?php


namespace mohamed205\Voicify\math;


use pocketmine\Player;

class DistanceMatrix
{

    private array $distances = [];

    public function add(string $player, int $distance)
    {
        $this->distances[strtolower($player)] = $distance;
    }

    public function getDistance(Player $player) : ?int
    {
        return $this->distances[strtolower($player)] ?? null;
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString()
    {
        return json_encode($this->distances);
    }

}