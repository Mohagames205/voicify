<?php


namespace mohamed205\Voicify\math;


class DistanceMatrix
{

    private array $distances = [];

    public function add(string $player, int $distance)
    {
        $this->distances[strtolower($player)] = $distance;
    }

    public function getDistance(string $player): ?int
    {
        return $this->distances[strtolower($player)] ?? null;
    }

    /**
     * @return array
     */
    public function getDistances(): array
    {
        return $this->distances;
    }

    public function toString(): string
    {
        return (string)$this;
    }

    public function __toString()
    {
        return json_encode($this->distances);
    }

}