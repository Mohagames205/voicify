<?php


namespace Mohamed205\voxum\math;


class Distance
{

    private string $primaryPlayer;

    private DistanceMatrix $distanceMatrix;

    public function __construct(string $primaryPlayer, DistanceMatrix $distanceMatrix)
    {
        $this->primaryPlayer = $primaryPlayer;
        $this->distanceMatrix = $distanceMatrix;
    }

    public function __toString()
    {
        return json_encode([strtolower($this->primaryPlayer) => $this->distanceMatrix->toString()]);
    }

}